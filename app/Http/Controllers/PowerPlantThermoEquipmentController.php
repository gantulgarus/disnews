<?php

namespace App\Http\Controllers;

use App\Models\PowerPlantThermoEquipment;
use App\Models\PowerPlant;
use Illuminate\Http\Request;

class PowerPlantThermoEquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $equipments = PowerPlantThermoEquipment::with('powerPlant')
            ->latest()
            ->paginate(15);

        return view('power-plant-thermo-equipments.index', compact('equipments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $powerPlants = PowerPlant::all();
        return view('power-plant-thermo-equipments.create', compact('powerPlants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
        ]);

        PowerPlantThermoEquipment::create($validated);

        return redirect()
            ->route('power-plant-thermo-equipments.index')
            ->with('success', 'Тоног төхөөрөмж амжилттай бүртгэгдлээ.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PowerPlantThermoEquipment $powerPlantThermoEquipment)
    {
        $powerPlantThermoEquipment->load('powerPlant');
        return view('power-plant-thermo-equipments.show', compact('powerPlantThermoEquipment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PowerPlantThermoEquipment $powerPlantThermoEquipment)
    {
        $powerPlants = PowerPlant::all();
        return view('power-plant-thermo-equipments.edit', compact('powerPlantThermoEquipment', 'powerPlants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PowerPlantThermoEquipment $powerPlantThermoEquipment)
    {
        $validated = $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
        ]);

        $powerPlantThermoEquipment->update($validated);

        return redirect()
            ->route('power-plant-thermo-equipments.index')
            ->with('success', 'Тоног төхөөрөмж амжилттай шинэчлэгдлээ.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PowerPlantThermoEquipment $powerPlantThermoEquipment)
    {
        $powerPlantThermoEquipment->delete();

        return redirect()
            ->route('power-plant-thermo-equipments.index')
            ->with('success', 'Тоног төхөөрөмж амжилттай устгагдлав.');
    }
}
