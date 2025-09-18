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
        $powerPlants = PowerPlant::with(['boilers', 'turbineGenerators'])->get();

        // Өдөр тутмын төлөвийн мэдээлэлтэй станцуудын жагсаалт
        $dailyReports = PowerPlantDailyReport::latest('created_at')
            ->get()
            ->groupBy('power_plant_id');

        return view('power_plants.index', compact('powerPlants', 'dailyReports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $powerPlants = PowerPlant::all(); // Станцын жагсаалт
        return view('power_plants.create', compact('powerPlants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'boilers.*.name' => 'required|string',
            'turbine_generators.*.name' => 'required|string',
            'status' => 'required|string',
            'report_date' => 'required|date',
        ]);

        // Станцын мэдээлэл олж авах
        $powerPlant = PowerPlant::findOrFail($request->power_plant_id);

        // Зуухны мэдээллийг хадгалах
        foreach ($request->boilers as $boiler) {
            $boilerInstance = Boiler::create([
                'power_plant_id' => $powerPlant->id,
                'name' => $boiler['name']
            ]);
            $powerPlant->boilers()->save($boilerInstance);
        }

        // Турбингенераторын мэдээллийг хадгалах
        foreach ($request->turbine_generators as $turbineGenerator) {
            $turbineGeneratorInstance = TurbineGenerator::create([
                'power_plant_id' => $powerPlant->id,
                'name' => $turbineGenerator['name']
            ]);
            $powerPlant->turbineGenerators()->save($turbineGeneratorInstance);
        }

        // Төлөвийн мэдээллийг PowerPlantDailyReport-д хадгалах
        PowerPlantDailyReport::create([
            'report_date' => $request->report_date,
            'boiler_id' => $request->boiler_id,
            'turbine_generator_id' => $request->turbine_generator_id,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->route('power-plants.index')->with('success', 'Станц, зуух, турбингенератор амжилттай нэмэгдлээ.');
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
            'boilers.*.name' => 'required|string|max:255',
            'turbine_generators.*.id' => 'nullable|exists:turbine_generators,id',
            'turbine_generators.*.name' => 'required|string|max:255',
        ]);

        // Update main power plant info
        $powerPlant->update($request->only('name', 'short_name'));

        // Keep track of existing boiler and turbine IDs
        $updatedBoilerIds = [];
        foreach ($request->boilers as $boilerData) {
            if (!empty($boilerData['id'])) {
                $boiler = Boiler::find($boilerData['id']);
                if ($boiler && $boiler->power_plant_id == $powerPlant->id) {
                    $boiler->update(['name' => $boilerData['name']]);
                    $updatedBoilerIds[] = $boiler->id;
                }
            } else {
                $newBoiler = Boiler::create([
                    'power_plant_id' => $powerPlant->id,
                    'name' => $boilerData['name'],
                ]);
                $updatedBoilerIds[] = $newBoiler->id;
            }
        }

        // Delete removed boilers
        $powerPlant->boilers()->whereNotIn('id', $updatedBoilerIds)->delete();

        // Handle turbine generators
        $updatedTurbineIds = [];
        foreach ($request->turbine_generators as $turbineData) {
            if (!empty($turbineData['id'])) {
                $turbine = TurbineGenerator::find($turbineData['id']);
                if ($turbine && $turbine->power_plant_id == $powerPlant->id) {
                    $turbine->update(['name' => $turbineData['name']]);
                    $updatedTurbineIds[] = $turbine->id;
                }
            } else {
                $newTurbine = TurbineGenerator::create([
                    'power_plant_id' => $powerPlant->id,
                    'name' => $turbineData['name'],
                ]);
                $updatedTurbineIds[] = $newTurbine->id;
            }
        }

        // Delete removed turbines
        $powerPlant->turbineGenerators()->whereNotIn('id', $updatedTurbineIds)->delete();

        return redirect()->route('power-plants.index')->with('success', 'Станцын мэдээлэл амжилттай шинэчлэгдлээ.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PowerPlant $powerPlant)
    {
        //
    }
}