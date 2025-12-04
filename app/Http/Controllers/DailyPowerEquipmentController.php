<?php

namespace App\Http\Controllers;

use App\Models\DailyPowerEquipment;
use App\Models\PowerPlant;
use Illuminate\Http\Request;

class DailyPowerEquipmentController extends Controller
{
    public function index()
    {
        $equipments = DailyPowerEquipment::with('powerPlant')->get();
        return view('daily_power_equipments.index', compact('equipments'));
    }

    public function create()
    {
        $powerPlants = PowerPlant::all();
        return view('daily_power_equipments.create', compact('powerPlants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'power_equipment' => 'required|string|max:255',
        ]);

        DailyPowerEquipment::create($request->all());
        return redirect()->route('daily_power_equipments.index')->with('success', 'Equipment added successfully.');
    }

    public function edit(DailyPowerEquipment $dailyPowerEquipment)
    {
        $powerPlants = PowerPlant::all();
        return view('daily_power_equipments.edit', compact('dailyPowerEquipment', 'powerPlants'));
    }

    public function update(Request $request, DailyPowerEquipment $dailyPowerEquipment)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'power_equipment' => 'required|string|max:255',
        ]);

        $dailyPowerEquipment->update($request->all());
        return redirect()->route('daily_power_equipments.index')->with('success', 'Equipment updated successfully.');
    }

    public function destroy(DailyPowerEquipment $dailyPowerEquipment)
    {
        $dailyPowerEquipment->delete();
        return redirect()->route('daily_power_equipments.index')->with('success', 'Equipment deleted successfully.');
    }
}
