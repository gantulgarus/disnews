<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\PowerPlant;
use Illuminate\Http\Request;
use App\Models\EquipmentStatus;
use App\Models\StationPowerInfo;

class DailyEquipmentReportController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));

        $powerPlants = PowerPlant::with([
            'equipmentStatuses' => function ($q) use ($date) {
                $q->whereDate('date', $date);
            },
            'powerInfos' => function ($q) use ($date) {
                $q->whereDate('date', $date);
            }
        ])->orderBy('Order')->get();


        // dd($powerPlants);

        return view('equipment-status.index', compact('powerPlants', 'date'));
    }


    // public function create()
    // {
    //     $powerPlants = PowerPlant::all();
    //     return view('equipment-status.create', compact('powerPlants'));
    // }
    public function create($powerPlantId)
    {
        $powerPlant = PowerPlant::findOrFail($powerPlantId);

        // Хамгийн сүүлийн power info татах
        $lastPowerInfo = StationPowerInfo::where('power_plant_id', $powerPlantId)
            ->orderByDesc('id')
            ->first();

        // Хамгийн сүүлийн тоноглолын төлөвүүд татах
        $lastEquipmentStatuses = EquipmentStatus::where('power_plant_id', $powerPlantId)
            ->whereDate('date', optional($lastPowerInfo)->date ?? now())
            ->get()
            ->keyBy('equipment_id');

        // Тухайн станцын бүх тоноглол
        $equipments = Equipment::with('type')
            ->where('power_plant_id', $powerPlantId)
            ->get();

        return view('equipment-status.create', compact(
            'powerPlant',
            'equipments',
            'lastEquipmentStatuses',
            'lastPowerInfo'
        ));
    }


    public function getEquipmentsByStation($powerPlantId)
    {
        $equipments = Equipment::with('type')->where('power_plant_id', $powerPlantId)->get();
        return response()->json($equipments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            'equipments' => 'required|array',
            'equipments.*.equipment_id' => 'required|exists:equipment,id',
            'equipments.*.status' => 'required|string',
            'equipments.*.remark' => 'nullable|string',
            'p' => 'nullable|numeric',
            'p_max' => 'nullable|numeric',
            'p_min' => 'nullable|numeric',
            'produced_energy' => 'nullable|numeric',
            'distributed_energy' => 'nullable|numeric',
            'main_equipment_remark' => 'nullable|string',
            'water_level' => 'nullable|numeric',
            'fuel_amount' => 'nullable|numeric',
        ]);

        foreach ($request->equipments as $eq) {
            EquipmentStatus::create([
                'power_plant_id' => $request->power_plant_id,
                'equipment_id' => $eq['equipment_id'],
                'status' => $eq['status'],
                'remark' => $eq['remark'] ?? null,
                'date' => $request->date,
            ]);
        }

        StationPowerInfo::create([
            'power_plant_id' => $request->power_plant_id,
            'p' => $request->p,
            'p_max' => $request->p_max,
            'p_min' => $request->p_min,
            'produced_energy' => $request->produced_energy,
            'distributed_energy' => $request->distributed_energy,
            'water_level' => $request->water_level,
            'fuel_amount' => $request->fuel_amount,
            'remark' => $request->main_equipment_remark,
            'date' => $request->date,
        ]);

        // ********** REGION шалгаад redirect хийх **********
        $plant = PowerPlant::find($request->power_plant_id);

        // region == 'ТБЭХС' бол reports.dailyReport, бусад бол reports.localDailyReport
        if ($plant->region === 'ТБЭХС') {
            return redirect()
                ->route('reports.dailyReport')
                ->with('success', 'Мэдээлэл амжилттай хадгалагдлаа');
        }

        return redirect()
            ->route('reports.localDailyReport')
            ->with('success', 'Мэдээлэл амжилттай хадгалагдлаа');
    }

    public function edit($powerPlantId, Request $request)
    {
        $powerPlants = PowerPlant::all();
        $date = $request->input('date', now()->format('Y-m-d'));

        $powerPlant = PowerPlant::with([
            'equipmentStatuses' => function ($q) use ($date) {
                $q->whereDate('date', $date);
            },
            'powerInfos' => function ($q) use ($date) {
                $q->whereDate('date', $date);
            }
        ])->findOrFail($powerPlantId);

        return view('equipment-status.edit', compact('powerPlant', 'date', 'powerPlants'));
    }

    public function update(Request $request, $powerPlantId)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'equipments' => 'required|array',
            'equipments.*.equipment_id' => 'required|exists:equipment,id',
            'equipments.*.status' => 'required|string',
            'equipments.*.remark' => 'nullable|string',
            'p' => 'nullable|numeric',
            'p_max' => 'nullable|numeric',
            'p_min' => 'nullable|numeric',
            'produced_energy' => 'nullable|numeric',
            'distributed_energy' => 'nullable|numeric',
            'main_equipment_remark' => 'nullable|string',
        ]);

        $date = $request->date;

        // Update or create equipment statuses
        foreach ($request->equipments as $eq) {
            EquipmentStatus::updateOrCreate(
                [
                    'power_plant_id' => $powerPlantId,
                    'equipment_id' => $eq['equipment_id'],
                    'date' => $date,
                ],
                [
                    'status' => $eq['status'],
                    'remark' => $eq['remark'] ?? null,
                ]
            );
        }

        // Update or create main power info
        StationPowerInfo::updateOrCreate(
            [
                'power_plant_id' => $powerPlantId,
                'date' => $date,
            ],
            [
                'p' => $request->p,
                'p_max' => $request->p_max,
                'p_min' => $request->p_min,
                'produced_energy' => $request->produced_energy,
                'distributed_energy' => $request->distributed_energy,
                'remark' => $request->main_equipment_remark,
            ]
        );

        return redirect()->route('daily-equipment-report.index')
            ->with('success', 'Мэдээлэл амжилттай шинэчлэгдлээ');
    }

    public function details($powerPlantId)
    {
        $powerPlant = PowerPlant::findOrFail($powerPlantId);

        // Тоноглолын төлөвүүд + тоноглолын мэдээлэлтэй хамт татах
        $statuses = EquipmentStatus::with('equipment')
            ->where('power_plant_id', $powerPlantId)
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();

        return view('equipment-status.details', compact('powerPlant', 'statuses'));
    }

    public function destroy($powerPlantId)
    {
        // Тухайн станцын тухайн өдрийн мэдээллийг устгах
        EquipmentStatus::where('power_plant_id', $powerPlantId)->delete();
        StationPowerInfo::where('power_plant_id', $powerPlantId)->delete();

        return redirect()->route('daily-equipment-report.index')
            ->with('success', 'Станцын мэдээ устгагдлаа');
    }
}
