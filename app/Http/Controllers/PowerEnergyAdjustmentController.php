<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PowerEnergyAdjustment;

class PowerEnergyAdjustmentController extends Controller
{
    // Бүх өгөгдөл харах
    public function index()
    {
        $adjustments = PowerEnergyAdjustment::latest()->paginate(15);
        return view('power_energy_adjustments.index', compact('adjustments'));
    }

    // Шинээр нэмэх form
    public function create()
    {
        return view('power_energy_adjustments.create');
    }

    // Шинээр хадгалах
    public function store(Request $request)
    {
        $request->validate([
            'restricted_kwh' => 'required|numeric|min:0',
            'discounted_kwh' => 'required|numeric|min:0',
            'date' => 'required|date|unique:power_energy_adjustments,date',
        ]);

        PowerEnergyAdjustment::create($request->all());

        return redirect()->route('reports.dailyReport')
            ->with('success', 'Мэдээлэл амжилттай хадгалагдлаа');
    }

    // Өгөгдөл харах
    public function show(PowerEnergyAdjustment $powerEnergyAdjustment)
    {
        return view('power_energy_adjustments.show', compact('powerEnergyAdjustment'));
    }

    // Өгөгдөл засах form
    public function edit(PowerEnergyAdjustment $powerEnergyAdjustment)
    {
        return view('power_energy_adjustments.edit', compact('powerEnergyAdjustment'));
    }

    // Өгөгдөл update хийх
    public function update(Request $request, PowerEnergyAdjustment $powerEnergyAdjustment)
    {
        $request->validate([
            'restricted_kwh' => 'required|numeric|min:0',
            'discounted_kwh' => 'required|numeric|min:0',
            'date' => 'required|date|unique:power_energy_adjustments,date,' . $powerEnergyAdjustment->id,
        ]);

        $powerEnergyAdjustment->update($request->all());

        return redirect()->route('power-energy-adjustments.index')
            ->with('success', 'Мэдээлэл амжилттай шинэчлэгдлээ');
    }

    // Өгөгдөл устгах
    public function destroy(PowerEnergyAdjustment $powerEnergyAdjustment)
    {
        $powerEnergyAdjustment->delete();

        return redirect()->route('power-energy-adjustments.index')
            ->with('success', 'Мэдээлэл устгагдлаа');
    }
}
