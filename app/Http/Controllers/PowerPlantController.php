<?php

namespace App\Http\Controllers;

use App\Models\Boiler;
use App\Models\PowerPlant;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\PowerPlantType;
use App\Models\TurbineGenerator;
use App\Models\PowerPlantDailyReport;

class PowerPlantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Станцуудын жагсаалт
        $powerPlants = PowerPlant::orderBy('Order')->get();

        return view('power_plants.index', compact('powerPlants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $powerPlantTypes = PowerPlantType::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        return view('power_plants.create', compact('powerPlantTypes', 'organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'power_plant_type_id' => 'required|exists:power_plant_types,id',
            'region' => 'required|string',
            'Order' => 'required|integer',
            'organization_id' => 'required|exists:organizations,id',
        ]);

        PowerPlant::create($request->only('name', 'short_name', 'power_plant_type_id', 'region', 'Order', 'organization_id'));

        return redirect()->route('power-plants.index')->with('success', 'Шинэ станц амжилттай бүртгэгдлээ.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PowerPlant $powerPlant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PowerPlant $powerPlant)
    {
        $powerPlantTypes = PowerPlantType::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();

        return view('power_plants.edit', compact('powerPlant', 'powerPlantTypes', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PowerPlant $powerPlant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'power_plant_type_id' => 'required|exists:power_plant_types,id',
            'region' => 'required|string',
            'Order' => 'required|integer',
            'organization_id' => 'required|exists:organizations,id',
        ]);

        // Update main power plant info
        $powerPlant->update($request->only('name', 'short_name', 'power_plant_type_id', 'region', 'Order', 'organization_id'));

        return redirect()->route('power-plants.index')->with('success', 'Станцын мэдээлэл амжилттай шинэчлэгдлээ.');
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PowerPlant $powerPlant)
    {
        // Станцыг устгахын өмнө холбоотой мэдээллийг шалгах
        $hasReports = PowerPlantDailyReport::where('power_plant_id', $powerPlant->id)->exists();
        $hasBoilers = Boiler::where('power_plant_id', $powerPlant->id)->exists();
        $hasTurbines = TurbineGenerator::where('power_plant_id', $powerPlant->id)->exists();

        if ($hasReports || $hasBoilers || $hasTurbines) {
            return redirect()->route('power-plants.index')->with('error', 'Энэ станцыг устгах боломжгүй. Холбоотой мэдээлэл байна.');
        }

        $powerPlant->delete();

        return redirect()->route('power-plants.index')->with('success', 'Станц амжилттай устгагдлаа.');
    }

    // --- 2. Зуух, турбин нэмэх хуудас ---
    public function addEquipment(PowerPlant $powerPlant)
    {
        $powerPlants = PowerPlant::orderBy('name')->get();
        return view('power_plants.add_equipment', compact('powerPlants', 'powerPlant'));
    }


    // --- 2. Зуух, турбин хадгалах ---
    public function storeEquipment(Request $request)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'boilers.*.name' => 'required|string',
            'turbine_generators.*.name' => 'required|string',
        ]);

        $powerPlant = PowerPlant::findOrFail($request->power_plant_id);

        foreach ($request->boilers as $boiler) {
            $powerPlant->boilers()->create(['name' => $boiler['name']]);
        }

        foreach ($request->turbine_generators as $tg) {
            $powerPlant->turbineGenerators()->create(['name' => $tg['name']]);
        }

        return redirect()->route('power-plants.index')->with('success', 'Зуух болон турбин амжилттай нэмэгдлээ.');
    }
}
