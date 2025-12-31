<?php

namespace App\Http\Controllers;

use App\Models\PowerPlant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PowerPlantReading;
use App\Services\PowerPlantDataService;
use App\Models\PowerPlantThermoEquipment;

class PowerPlantReadingController extends Controller
{
    public function __construct(
        private PowerPlantDataService $service
    ) {}

    /**
     * Үндсэн хуудас харуулах
     */
    public function index(Request $request)
    {
        $userPowerPlant = auth()->user()->mainPowerPlant;

        if ($userPowerPlant) {
            $powerPlants = PowerPlant::where('id', $userPowerPlant->id)->get();
            $powerPlantId = $userPowerPlant->id;
        } else {
            $powerPlants = PowerPlant::mainStations()->get();
            $powerPlantId = $request->power_plant_id;
        }

        $date = $request->date ?? now()->format('Y-m-d');

        $readings = null;
        if ($date) {
            $query = PowerPlantReading::with('equipment.powerPlant')
                ->where('reading_date', $date);

            if ($powerPlantId) {
                $query->whereHas('equipment', function ($q) use ($powerPlantId) {
                    $q->where('power_plant_id', $powerPlantId);
                });
            }

            // Pagination
            $paginated = $query->orderBy('reading_hour', 'desc')->paginate(20);

            // Collection болгож цагээр групплэх
            $grouped = $paginated->getCollection()->groupBy('reading_hour');

            // Pagination-д зориулж буцааж тавих
            $readings = new \Illuminate\Pagination\LengthAwarePaginator(
                $grouped,
                $paginated->total(),
                $paginated->perPage(),
                $paginated->currentPage(),
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return view('power-plant-readings.index', compact('powerPlants', 'date', 'powerPlantId', 'readings'));
    }



    /**
     * API-аас өгөгдөл татах
     */
    public function fetch(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $result = $this->service->fetchAndStore($request->date);

        return response()->json($result);
    }

    /**
     * Гараар бүртгэх хуудас харуулах
     */
    public function create(Request $request)
    {
        $userPowerPlant = auth()->user()->mainPowerPlant;

        if ($userPowerPlant) {
            $powerPlants = PowerPlant::where('id', $userPowerPlant->id)->get();
            $equipments = PowerPlantThermoEquipment::where('power_plant_id', $userPowerPlant->id)->get();
            $powerPlantId = $userPowerPlant->id;
        } else {
            $powerPlants = PowerPlant::mainStations()->get();
            $equipments = PowerPlantThermoEquipment::all();
            $powerPlantId = null;
        }

        return view('power-plant-readings.create', compact(
            'powerPlants',
            'equipments',
            'powerPlantId'
        ));
    }

    /**
     * Гараар бүртгэсэн өгөгдлийг хадгалах
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'reading_date' => 'required|date',
            'reading_hour' => 'required|integer|min:1|max:24',
            'values' => 'required|array',
            'values.*' => 'nullable|numeric', // утга оруулаагүй бол skip хийнэ
        ]);

        $userPowerPlant = auth()->user()->mainPowerPlant;

        foreach ($request->values as $equipmentId => $value) {
            if ($value === null) continue; // утга хоосон байвал алгасна

            $equipment = PowerPlantThermoEquipment::find($equipmentId);
            if (!$equipment) continue;

            // Хэрэглэгч зөвхөн өөрийн станцын өгөгдлийг нэмэх боломжтой
            if ($userPowerPlant && $equipment->power_plant_id != $userPowerPlant->id) {
                continue;
            }

            // Давхардсан өгөгдөл байгаа эсэх
            $exists = PowerPlantReading::where('reading_date', $request->reading_date)
                ->where('reading_hour', $request->reading_hour)
                ->where('power_plant_thermo_equipment_id', $equipmentId)
                ->exists();

            if ($exists) continue;

            // Өгөгдөл хадгалах
            PowerPlantReading::create([
                'power_plant_thermo_equipment_id' => $equipmentId,
                'reading_date' => $request->reading_date,
                'reading_hour' => $request->reading_hour,
                'value' => $value,
            ]);
        }

        return redirect()->route('power-plant-readings.index')
            ->with('success', 'Бүх тоноглолын өгөгдлийг амжилттай хадгаллаа!');
    }

    /**
     * Тухайн өдөр, цагийн мэдээллийг засах
     */
    public function edit(Request $request)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            'hour' => 'required|integer|min:1|max:24',
        ]);

        $powerPlant = PowerPlant::findOrFail($request->power_plant_id);

        // Тухайн станцын бүх тоноглол
        $equipments = PowerPlantThermoEquipment::where('power_plant_id', $powerPlant->id)->get();

        // Тухайн өдөр, цагийн байгаа readings
        $existingReadings = PowerPlantReading::where('reading_date', $request->date)
            ->where('reading_hour', $request->hour)
            ->whereIn('power_plant_thermo_equipment_id', $equipments->pluck('id'))
            ->get()
            ->keyBy('power_plant_thermo_equipment_id');

        return view('power-plant-readings.edit', compact(
            'powerPlant',
            'equipments',
            'existingReadings',
            'request'
        ));
    }

    public function updateBulk(Request $request)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            'hour' => 'required|integer|min:1|max:24',
            'readings' => 'required|array',
        ]);

        foreach ($request->readings as $equipmentId => $value) {
            if ($value === null || $value === '') {
                // Хоосон утгыг алгасна
                continue;
            }

            PowerPlantReading::updateOrCreate(
                [
                    'power_plant_thermo_equipment_id' => $equipmentId,
                    'reading_date' => $request->date,
                    'reading_hour' => $request->hour,
                ],
                ['value' => $value]
            );
        }

        return redirect()->route('power-plant-readings.index', ['date' => $request->date])
            ->with('success', 'Мэдээлэл амжилттай хадгалагдлаа!');
    }

    public function destroyBulk(Request $request)
    {
        $request->validate([
            'power_plant_id' => 'required|integer',
            'date' => 'required|date',
            'hour' => 'required|integer',
        ]);

        PowerPlantReading::whereHas('equipment', function ($q) use ($request) {
            $q->where('power_plant_id', $request->power_plant_id);
        })
            ->where('reading_date', $request->date)
            ->where('reading_hour', $request->hour)
            ->delete();

        return redirect()->route('power-plant-readings.index', [
            'date' => $request->date,
            'power_plant_id' => $request->power_plant_id,
        ])->with('success', "{$request->hour} цагийн бүх мэдээлэл устлаа.");
    }




    /**
     * Өдрийн мэдээлэл харуулах (API)
     */
    public function show(Request $request) {}

    /**
     * Статистик мэдээлэл
     */
    public function statistics(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'equipment_code' => 'required|exists:power_plant_thermo_equipments,code',
        ]);

        $equipment = PowerPlantThermoEquipment::where('code', $request->equipment_code)->first();

        // Эрхийн шалгалт
        $userPowerPlant = auth()->user()->mainPowerPlant;
        if ($userPowerPlant && $equipment->power_plant_id != $userPowerPlant->id) {
            return response()->json([
                'error' => 'Та зөвхөн өөрийн станцын мэдээлэл харах эрхтэй!'
            ], 403);
        }

        $readings = PowerPlantReading::where('power_plant_thermo_equipment_id', $equipment->id)
            ->whereBetween('reading_date', [$request->start_date, $request->end_date])
            ->get();

        return response()->json([
            'equipment' => [
                'code' => $equipment->code,
                'name' => $equipment->name,
                'unit' => $equipment->unit,
            ],
            'period' => [
                'start' => $request->start_date,
                'end' => $request->end_date,
            ],
            'statistics' => [
                'count' => $readings->count(),
                'avg' => round($readings->avg('value'), 2),
                'min' => $readings->min('value'),
                'max' => $readings->max('value'),
                'sum' => round($readings->sum('value'), 2),
            ],
            'data' => $readings->map(function ($reading) {
                return [
                    'date' => $reading->reading_date->format('Y-m-d'),
                    'hour' => $reading->reading_hour,
                    'value' => $reading->value,
                ];
            })
        ]);
    }

    /**
     * Хамгийн сүүлийн өгөгдөл
     */
    public function latest()
    {
        $userPowerPlant = auth()->user()->mainPowerPlant;

        $latestDate = PowerPlantReading::max('reading_date');
        $latestHour = PowerPlantReading::where('reading_date', $latestDate)
            ->max('reading_hour');

        $query = PowerPlantReading::with('equipment.powerPlant')
            ->where('reading_date', $latestDate)
            ->where('reading_hour', $latestHour);

        // Эрхийн хязгаарлалт
        if ($userPowerPlant) {
            $query->whereHas('equipment', function ($q) use ($userPowerPlant) {
                $q->where('power_plant_id', $userPowerPlant->id);
            });
        }

        $readings = $query->get();

        return response()->json([
            'date' => $latestDate,
            'hour' => $latestHour,
            'data' => $readings->groupBy('equipment.powerPlant.name')->map(function ($plantReadings) {
                return $plantReadings->map(function ($reading) {
                    return [
                        'equipment' => $reading->equipment->name,
                        'code' => $reading->equipment->code,
                        'value' => $reading->value,
                        'unit' => $reading->equipment->unit,
                    ];
                });
            })
        ]);
    }

    /**
     * Өдрийн нэгтгэл харуулах
     */
    public function dailyOverview(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        $userPowerPlant = auth()->user()->mainPowerPlant;

        $query = PowerPlantReading::with(['equipment.powerPlant'])
            ->where('reading_date', $date);

        // Эрхийн хязгаарлалт
        if ($userPowerPlant) {
            $query->whereHas('equipment', function ($q) use ($userPowerPlant) {
                $q->where('power_plant_id', $userPowerPlant->id);
            });
        }

        $readings = $query->get();
        $powerPlantsData = collect();

        if ($readings->count() > 0) {
            $groupedByPlant = $readings->groupBy('equipment.powerPlant.name');

            foreach ($groupedByPlant as $plantName => $plantReadings) {
                $equipments = $plantReadings->pluck('equipment')->unique('id');

                $hourlyData = [];
                foreach ($plantReadings as $reading) {
                    $equipmentId = $reading->equipment->id;
                    $hour = $reading->reading_hour;

                    if (!isset($hourlyData[$equipmentId])) {
                        $hourlyData[$equipmentId] = [];
                    }

                    $hourlyData[$equipmentId][$hour] = $reading;
                }

                $powerPlantsData->put($plantName, [
                    'equipments' => $equipments,
                    'hourly_data' => $hourlyData,
                ]);
            }
        }

        $totalReadings = $readings->count();
        $hoursCovered = $readings->pluck('reading_hour')->unique()->count();

        return view('power-plant-readings.daily-overview', compact(
            'date',
            'powerPlantsData',
            'totalReadings',
            'hoursCovered'
        ));
    }

    /**
     * Температурын график
     */
    public function temperatureCharts(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        $userPowerPlant = auth()->user()->mainPowerPlant;

        $query = PowerPlantReading::with(['equipment.powerPlant'])
            ->where('reading_date', $date)
            ->whereHas('equipment', function ($query) {
                $query->where('code', 'like', '%t1%')
                    ->orWhere('code', 'like', '%t2%');
            });

        // Эрхийн хязгаарлалт
        if ($userPowerPlant) {
            $query->whereHas('equipment', function ($q) use ($userPowerPlant) {
                $q->where('power_plant_id', $userPowerPlant->id);
            });
        }

        $readings = $query->orderBy('reading_hour')->get();
        $chartData = [];

        if ($readings->count() > 0) {
            $groupedByPlant = $readings->groupBy('equipment.powerPlant.name');

            foreach ($groupedByPlant as $plantName => $plantReadings) {
                $t1Readings = $plantReadings->filter(fn($r) => stripos($r->equipment->code, 't1') !== false);
                $t2Readings = $plantReadings->filter(fn($r) => stripos($r->equipment->code, 't2') !== false);

                $t1Equipment = $t1Readings->first()?->equipment;
                $t2Equipment = $t2Readings->first()?->equipment;

                $hours = [];
                $t1Data = [];
                $t2Data = [];

                for ($hour = 1; $hour <= 24; $hour++) {
                    $hours[] = $hour . 'ц';

                    $t1Reading = $t1Readings->firstWhere('reading_hour', $hour);
                    $t1Data[] = $t1Reading ? (float) $t1Reading->value : null;

                    $t2Reading = $t2Readings->firstWhere('reading_hour', $hour);
                    $t2Data[] = $t2Reading ? (float) $t2Reading->value : null;
                }

                $slug = Str::slug($plantName);

                $chartData[$slug] = [
                    'plant_name' => $plantName,
                    'hours' => $hours,
                    't1_label' => $t1Equipment ? $t1Equipment->name : 'T1 Температур',
                    't1_data' => $t1Data,
                    't2_label' => $t2Equipment ? $t2Equipment->name : 'T2 Температур',
                    't2_data' => $t2Data,
                ];
            }
        }

        return view('power-plant-readings.temperature-charts', [
            'date' => $date,
            'chartData' => $chartData,
        ]);
    }
}
