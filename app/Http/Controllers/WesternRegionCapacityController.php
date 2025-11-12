<?php

namespace App\Http\Controllers;

use App\Models\WesternRegionCapacity;
use Illuminate\Http\Request;

class WesternRegionCapacityController extends Controller
{
    public function index()
    {
        $capacities = WesternRegionCapacity::latest()->paginate(10);
        return view('western_region_capacities.index', compact('capacities'));
    }

    public function create()
    {
        return view('western_region_capacities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'p_max' => 'required|numeric',
            'p_min' => 'required|numeric',
            'p_imp_max' => 'required|numeric',
            'p_imp_min' => 'required|numeric',
            'import_received' => 'required|numeric',
            'import_distributed' => 'required|numeric',
            'date' => 'required|date',
        ]);

        WesternRegionCapacity::create($validated);

        return redirect()->route('western_region_capacities.index')
            ->with('success', 'Амжилттай нэмлээ.');
    }

    public function show(WesternRegionCapacity $westernRegionCapacity)
    {
        return view('western_region_capacities.show', compact('westernRegionCapacity'));
    }

    public function edit(WesternRegionCapacity $westernRegionCapacity)
    {
        return view('western_region_capacities.edit', compact('westernRegionCapacity'));
    }

    public function update(Request $request, WesternRegionCapacity $westernRegionCapacity)
    {
        $validated = $request->validate([
            'p_max' => 'required|numeric',
            'p_min' => 'required|numeric',
            'p_imp_max' => 'required|numeric',
            'p_imp_min' => 'required|numeric',
            'import_received' => 'required|numeric',
            'import_distributed' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $westernRegionCapacity->update($validated);

        return redirect()->route('western_region_capacities.index')
            ->with('success', 'Мэдээлэл амжилттай шинэчлэгдлээ.');
    }

    public function destroy(WesternRegionCapacity $westernRegionCapacity)
    {
        $westernRegionCapacity->delete();

        return redirect()->route('western_region_capacities.index')
            ->with('success', 'Амжилттай устгалаа.');
    }
}
