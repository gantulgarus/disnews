<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\PowerPlant;
use App\Models\EquipmentType;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::with(['powerPlant', 'type'])->paginate(10);
        return view('equipments.index', compact('equipments'));
    }

    public function create()
    {
        $powerPlants = PowerPlant::all();
        $equipmentTypes = EquipmentType::all();
        return view('equipments.create', compact('powerPlants', 'equipmentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'equipment_type_id' => 'required|exists:equipment_types,id',
            'name' => 'required|string|max:255',
        ]);

        Equipment::create($validated);

        return redirect()->route('equipments.index')->with('success', 'Тоноглол амжилттай нэмэгдлээ.');
    }

    public function show(Equipment $equipment)
    {
        return view('equipments.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        $powerPlants = PowerPlant::all();
        $equipmentTypes = EquipmentType::all();
        return view('equipments.edit', compact('equipment', 'powerPlants', 'equipmentTypes'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'equipment_type_id' => 'required|exists:equipment_types,id',
            'name' => 'required|string|max:255',
        ]);

        $equipment->update($validated);

        return redirect()->route('equipments.index')->with('success', 'Тоноглолын мэдээлэл шинэчлэгдлээ.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('equipments.index')->with('success', 'Тоноглол устгагдлаа.');
    }
}
