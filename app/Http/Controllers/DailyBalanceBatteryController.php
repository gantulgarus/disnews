<?php

namespace App\Http\Controllers;

use App\Models\PowerPlant;
use Illuminate\Http\Request;
use App\Models\DailyBalanceBattery;

class DailyBalanceBatteryController extends Controller
{
    public function index()
    {
        $userOrgId = auth()->user()->organization_id;

        $items = DailyBalanceBattery::with('powerPlant')
            ->whereHas('powerPlant', function ($q) use ($userOrgId) {
                $q->where('organization_id', $userOrgId);
            })
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('daily_balance_batteries.index', compact('items'));
    }

    public function create()
    {
        $userOrgId = auth()->user()->organization_id;

        $plants = PowerPlant::where('organization_id', $userOrgId)->get();

        return view('daily_balance_batteries.create', compact('plants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            'energy_given' => 'required|numeric',
            'energy_taken' => 'required|numeric',
        ]);

        DailyBalanceBattery::create($request->all());

        return redirect()->route('daily-balance-batteries.index')
            ->with('success', 'Мэдээлэл амжилттай нэмэгдлээ.');
    }

    public function edit($id)
    {
        $userOrgId = auth()->user()->organization_id;

        $plants = PowerPlant::where('organization_id', $userOrgId)->get();

        $item = DailyBalanceBattery::findOrFail($id);

        return view('daily_balance_batteries.edit', compact('item', 'plants'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            'energy_given' => 'required|numeric',
            'energy_taken' => 'required|numeric',
        ]);

        $item = DailyBalanceBattery::findOrFail($id);
        $item->update($request->all());

        return redirect()->route('daily-balance-batteries.index')
            ->with('success', 'Мэдээлэл амжилттай засагдлаа.');
    }

    public function destroy($id)
    {
        DailyBalanceBattery::findOrFail($id)->delete();

        return redirect()->route('daily-balance-batteries.index')
            ->with('success', 'Мэдээлэл устгагдлаа.');
    }
}
