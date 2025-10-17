<?php

namespace App\Http\Controllers;

use App\Models\ElectricDailyRegime;
use App\Models\PowerPlant;
use Illuminate\Http\Request;

class ElectricDailyRegimeController extends Controller
{
    public function index()
    {
        $regimes = ElectricDailyRegime::latest()->paginate(10);
        return view('electric_daily_regimes.index', compact('regimes'));
    }

    public function create()
    {
        $powerPlants = PowerPlant::all();
        return view('electric_daily_regimes.create', compact('powerPlants'));
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            // Та бусад талбаруудыг хүсвэл validate хийх
        ]);

        $input['user_id'] = auth()->id(); // Одоогийн хэрэглэгчийн ID-г автоматаар оруулах

        ElectricDailyRegime::create($input);

        return redirect()->route('electric_daily_regimes.index')
            ->with('success', 'Мэдээлэл амжилттай нэмэгдлээ.');
    }

    public function show(ElectricDailyRegime $electricDailyRegime)
    {
        return view('electric_daily_regimes.show', compact('electricDailyRegime'));
    }

    public function edit(ElectricDailyRegime $electricDailyRegime)
    {
        $powerPlants = PowerPlant::all();
        return view('electric_daily_regimes.edit', compact('electricDailyRegime', 'powerPlants'));
    }

    public function update(Request $request, ElectricDailyRegime $electricDailyRegime)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
        ]);

        $electricDailyRegime->update($request->all());

        return redirect()->route('electric_daily_regimes.index')
            ->with('success', 'Мэдээлэл амжилттай шинэчлэгдлээ.');
    }

    public function destroy(ElectricDailyRegime $electricDailyRegime)
    {
        $electricDailyRegime->delete();

        return redirect()->route('electric_daily_regimes.index')
            ->with('success', 'Мэдээлэл амжилттай устгагдлаа.');
    }
}
