<?php

namespace App\Http\Controllers;

use App\Models\Boiler;
use App\Models\PowerPlant;
use Illuminate\Http\Request;
use App\Models\TurbineGenerator;
use App\Models\PowerPlantDailyReport;

class PowerPlantDailyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = PowerPlantDailyReport::latest()->paginate(10);
        return view('power_plant_daily_reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     $boilers = Boiler::all();
    //     $turbines = TurbineGenerator::all();
    //     return view('power_plant_daily_reports.create', compact('boilers', 'turbines'));
    // }
    public function create(Request $request)
    {
        $powerPlantId = $request->get('power_plant_id');
        $powerPlant = PowerPlant::with(['boilers', 'turbineGenerators'])->findOrFail($powerPlantId);

        // тухайн станцын хамгийн сүүлийн мэдээг авах
        $lastReport = PowerPlantDailyReport::where('power_plant_id', $powerPlantId)
            ->latest('created_at')
            ->first();

        return view('power_plant_daily_reports.create', compact('powerPlant', 'lastReport'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'boiler_working' => 'nullable|array',
            'boiler_preparation' => 'nullable|array',
            'boiler_repair' => 'nullable|array',
            'turbine_working' => 'nullable|array',
            'turbine_preparation' => 'nullable|array',
            'turbine_repair' => 'nullable|array',
            'power' => 'nullable|numeric',
            'power_max' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        // Create new PowerPlantDailyReport instance
        $report = new PowerPlantDailyReport();
        $report->power_plant_id = $validated['power_plant_id'];
        $report->report_date = now();

        // Encode arrays to JSON
        $report->boiler_working = isset($validated['boiler_working']) ? json_encode($validated['boiler_working']) : null;
        $report->boiler_preparation = isset($validated['boiler_preparation']) ? json_encode($validated['boiler_preparation']) : null;
        $report->boiler_repair = isset($validated['boiler_repair']) ? json_encode($validated['boiler_repair']) : null;
        $report->turbine_working = isset($validated['turbine_working']) ? json_encode($validated['turbine_working']) : null;
        $report->turbine_preparation = isset($validated['turbine_preparation']) ? json_encode($validated['turbine_preparation']) : null;
        $report->turbine_repair = isset($validated['turbine_repair']) ? json_encode($validated['turbine_repair']) : null;
        $report->power = $validated['power'] ?? null;
        $report->power_max = $validated['power_max'] ?? null;
        $report->notes = $validated['notes'] ?? null;


        // Save the report
        $report->save();

        // Redirect with success message
        return redirect()->route('power-plants.index')->with('success', 'Тайлан амжилттай хадгалагдлаа.');
    }




    /**
     * Display the specified resource.
     */
    public function show(PowerPlantDailyReport $powerPlantDailyReport)
    {
        return view('power_plant_daily_reports.show', compact('powerPlantDailyReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PowerPlantDailyReport $powerPlantDailyReport)
    {
        $boilers = Boiler::all();
        $turbines = TurbineGenerator::all();
        return view('power_plant_daily_reports.edit', compact('powerPlantDailyReport', 'boilers', 'turbines'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PowerPlantDailyReport $powerPlantDailyReport)
    {
        $request->validate([
            'report_date' => 'required|date',
            'boiler_id' => 'nullable|exists:boilers,id',
            'turbine_generator_id' => 'nullable|exists:turbine_generators,id',
            'status' => 'required|in:Ажилд,Бэлтгэлд,Засварт',
            'notes' => 'nullable|string',
        ]);

        $powerPlantDailyReport->update($request->all());

        return redirect()->route('power-plant-daily-reports.index')->with('success', 'Тайлан амжилттай шинэчлэгдлээ.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PowerPlantDailyReport $powerPlantDailyReport)
    {
        $powerPlantDailyReport->delete();

        return redirect()->route('power-plant-daily-reports.index')->with('success', 'Тайлан устгагдлаа.');
    }

    public function status()
    {
        $powerPlants = PowerPlant::with(['boilers', 'turbineGenerators'])->get();

        // Өдөр тутмын төлөвийн мэдээлэлтэй станцуудын жагсаалт
        $dailyReports = PowerPlantDailyReport::latest('created_at')
            ->get()
            ->groupBy('power_plant_id');

        return view('power_plant_daily_reports.status', compact('dailyReports', 'powerPlants'));
    }
}
