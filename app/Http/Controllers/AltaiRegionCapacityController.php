<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AltaiRegionCapacity;

class AltaiRegionCapacityController extends Controller
{
    public function index()
    {
        $items = AltaiRegionCapacity::orderBy('date', 'desc')->paginate(20);
        return view('altai_region_capacity.index', compact('items'));
    }

    public function create()
    {
        return view('altai_region_capacity.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:altai_region_capacities,date',
            'max_load' => 'nullable|numeric',
            'min_load' => 'nullable|numeric',
            'import_from_bbexs' => 'nullable|numeric',
            'import_from_tbns' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        AltaiRegionCapacity::create($validated);

        return redirect()->route('altai-region-capacity.index')
            ->with('success', 'Мэдээлэл амжилттай хадгалагдлаа');
    }

    public function edit($id)
    {
        $item = AltaiRegionCapacity::findOrFail($id);
        return view('altain_region_capacity.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = AltaiRegionCapacity::findOrFail($id);

        $validated = $request->validate([
            'date' => 'required|date|unique:altai_region_capacities,date,' . $id,
            'max_load' => 'nullable|numeric',
            'min_load' => 'nullable|numeric',
            'import_from_bbexs' => 'nullable|numeric',
            'import_from_tbns' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->route('altai-region-capacity.index')
            ->with('success', 'Мэдээлэл шинэчлэгдлээ');
    }

    public function destroy($id)
    {
        AltaiRegionCapacity::destroy($id);

        return redirect()->route('altai-region-capacity.index')
            ->with('success', 'Амжилттай устгагдлаа');
    }
}