<?php

namespace App\Http\Controllers;

use App\Models\Boiler;
use App\Models\PowerPlant;
use Illuminate\Http\Request;
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
        $powerPlants = PowerPlant::orderBy('name')->get();

        return view('power_plants.index', compact('powerPlants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('power_plants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'z' => 'nullable|string',
            't' => 'nullable|string',
        ]);

        PowerPlant::create($request->only('name', 'short_name', 'z', 't'));

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
        return view('power_plants.edit', compact('powerPlant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PowerPlant $powerPlant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'boilers.*.id' => 'nullable|exists:boilers,id',
            'boilers.*.name' => 'nullable|string|max:255',
            'turbine_generators.*.id' => 'nullable|exists:turbine_generators,id',
            'turbine_generators.*.name' => 'nullable|string|max:255',
        ]);

        // Update main power plant info
        $powerPlant->update($request->only('name', 'short_name'));

        // === Handle Boilers ===
        $updatedBoilerIds = [];
        if ($request->has('boilers') && is_array($request->boilers)) {
            foreach ($request->boilers as $boilerData) {
                if (!empty($boilerData['id'])) {
                    $boiler = Boiler::find($boilerData['id']);
                    if ($boiler && $boiler->power_plant_id == $powerPlant->id) {
                        $boiler->update(['name' => $boilerData['name']]);
                        $updatedBoilerIds[] = $boiler->id;
                    }
                } elseif (!empty($boilerData['name'])) {
                    $newBoiler = Boiler::create([
                        'power_plant_id' => $powerPlant->id,
                        'name' => $boilerData['name'],
                    ]);
                    $updatedBoilerIds[] = $newBoiler->id;
                }
            }
        }

        // Delete removed boilers
        if (count($updatedBoilerIds)) {
            $powerPlant->boilers()->whereNotIn('id', $updatedBoilerIds)->delete();
        } else {
            $powerPlant->boilers()->delete(); // Хэрэв хоосон ирвэл бүгдийг устгах
        }

        // === Handle Turbine Generators ===
        $updatedTurbineIds = [];
        if ($request->has('turbine_generators') && is_array($request->turbine_generators)) {
            foreach ($request->turbine_generators as $turbineData) {
                if (!empty($turbineData['id'])) {
                    $turbine = TurbineGenerator::find($turbineData['id']);
                    if ($turbine && $turbine->power_plant_id == $powerPlant->id) {
                        $turbine->update(['name' => $turbineData['name']]);
                        $updatedTurbineIds[] = $turbine->id;
                    }
                } elseif (!empty($turbineData['name'])) {
                    $newTurbine = TurbineGenerator::create([
                        'power_plant_id' => $powerPlant->id,
                        'name' => $turbineData['name'],
                    ]);
                    $updatedTurbineIds[] = $newTurbine->id;
                }
            }
        }

        // Delete removed turbines
        if (count($updatedTurbineIds)) {
            $powerPlant->turbineGenerators()->whereNotIn('id', $updatedTurbineIds)->delete();
        } else {
            $powerPlant->turbineGenerators()->delete();
        }

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
