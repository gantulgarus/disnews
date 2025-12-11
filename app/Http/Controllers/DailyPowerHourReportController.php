<?php

namespace App\Http\Controllers;

use App\Models\PowerPlant;
use Illuminate\Http\Request;
use App\Models\DailyPowerEquipment;
use App\Models\DailyPowerHourReport;
use Illuminate\Support\Facades\Auth;

class DailyPowerHourReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Хэрэглэгчийн байгууллагын станцыг олно (нэг л ширхэг)
        $plant = $user->mainPowerPlant;

        $date = $request->date ?? date('Y-m-d');

        // тухайн станцын бүх мэдээ
        $reports = DailyPowerHourReport::where('power_plant_id', $plant->id)
            ->where('date', $date)
            ->orderBy('time', 'asc')
            ->get();

        // станцын бүх тоноглол
        $equipments = DailyPowerEquipment::where('power_plant_id', $plant->id)->get();

        // Pivot
        $pivot = [];

        foreach ($reports as $r) {
            $t = $r->time;

            if (!isset($pivot[$t])) {
                $pivot[$t] = [
                    'id'   => $r->id,   // ← ID нэмэгдлээ
                    'time' => $t,
                    'date' => $r->date, 
                    'powerPlantId' => $r->power_plant_id, 

                ];
            }

                $equipName = $r->equipment->equipment_name;
                $pivot[$t][$equipName] = $r->power_value;
                }

        $pivot = array_values($pivot);

        return view('daily_power_hour_reports.index', compact('equipments', 'pivot', 'date'));
    }

    public function create()
    {
        $user = Auth::user();

        // Хэрэглэгчийн байгууллагын станцыг олно (нэг л ширхэг)
        $plant = $user->mainPowerPlant;
        // $plant = PowerPlant::mainStations()->where('organization_id', $org_id)->first();

        if (!$plant) {
            return redirect()->back()->with('error', 'Таны байгууллагад станц бүртгэлгүй байна!');
        }

        // Тухайн станцын тоноглолуудыг авна
        $equipments = DailyPowerEquipment::where('power_plant_id', $plant->id)->get();

        // dd($equipments);

        return view('daily_power_hour_reports.create', compact('plant', 'equipments'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            'time' => 'required',
            'equipments' => 'required|array',
        ]);

        $userId = Auth::id();

        foreach ($request->equipments as $eq) {
            if (!empty($eq['power_value'])) {
                DailyPowerHourReport::create([
                    'power_plant_id' => $request->power_plant_id,
                    'daily_power_equipment_id' => $eq['id'],
                    'power_value' => $eq['power_value'],
                    'date' => $request->date,
                    'time' => $request->time,
                    'user_id' => $userId,
                ]);
            }
        }

        return redirect()->route('daily_power_hour_reports.index')
            ->with('success', 'Ачааллын цагийн мэдээг амжилттай хадгаллаа.');
    }



    
   public function edit($powerPlantId, $time)
        {
            $plant = PowerPlant::findOrFail($powerPlantId);
            $equipments = DailyPowerEquipment::where('power_plant_id', $powerPlantId)->get();
            $records = DailyPowerHourReport::where('power_plant_id', $powerPlantId)
                        ->where('time', $time)
                        ->get()
                        ->keyBy('daily_power_equipment_id');
            $date = now()->toDateString();

            return view('daily_power_hour_reports.edit', compact('plant', 'equipments', 'records', 'powerPlantId', 'time', 'date'));
        }
        
    public function update(Request $request, $powerPlantId, $time)
        {
            $records = $request->input('power_value', []); // [equipment_id => value]
            $date = $request->input('date', now()->toDateString());
            $userId = Auth::id();

            foreach ($records as $equipmentId => $value) {
                DailyPowerHourReport::updateOrCreate(
                    [
                        'power_plant_id' => $powerPlantId,
                        'daily_power_equipment_id' => $equipmentId,
                        'date' => $date,
                        'time' => $time, // цаг параметр
                    ],
                    [
                        'power_value' => $value,
                        'user_id' => $userId,
                    ]
                );
            }

            return redirect()->route('daily_power_hour_reports.editByPlantAndTime', [$powerPlantId, $time])
                            ->with('success', 'Өгөгдөл амжилттай шинэчлэгдлээ!');

          
        } 

    public function userPowerReport(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));

        // Цагууд
        $times = DailyPowerHourReport::where('date', $date)
            ->select('time')
            ->distinct()
            ->orderBy('time')
            ->pluck('time')
            ->toArray();

        // Хэрэглэгчид + станц + equipment-тай report-уудыг авна
        $reports = DailyPowerHourReport::with(['powerPlant', 'equipment'])
            ->where('date', $date)
            ->orderBy('power_plant_id')
            ->orderBy('daily_power_equipment_id')
            ->orderBy('time')
            ->get();

        // Pivot data бэлтгэх
        $data = [];
        foreach ($reports as $r) {
            $plantId = $r->power_plant_id;
            $equipId = $r->daily_power_equipment_id;

            $rowKey = $plantId . '_' . $equipId;

            if (!isset($data[$rowKey])) {
                $data[$rowKey] = [
                    'powerPlant' => $r->powerPlant,
                    'equipment' => $r->equipment,
                ];

                // Цагийн бүх баганыг эхлүүлж "-"
                foreach ($times as $time) {
                    $data[$rowKey][$time] = '-';
                }
            }
            // тухайн цагийн утгыг оруулах
            $data[$rowKey][$r->time] = $r->power_value;
        }
        // dd($data);

        return view('daily_power_hour_reports.report', compact('times', 'data', 'date'));
    }


    public function show($id)
    {
        $report = DailyPowerHourReport::findOrFail($id);
        return view('daily_power_hour_reports.show', compact('report'));
    }
}
