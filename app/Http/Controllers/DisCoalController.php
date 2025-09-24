<?php

namespace App\Http\Controllers;

use App\Models\DisCoal;
use Illuminate\Http\Request;

class DisCoalController extends Controller
{

    public function index()
    {
        $coals = DisCoal::latest()->paginate(10); // pagination
        return view('dis_coal.index', compact('coals'));
    }

    public function create()
    {
        return view('dis_coal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
        'date' => 'required|date',
        'CAME_TRAIN' => 'nullable|integer',
        'UNLOADING_TRAIN' => 'nullable|integer',
        'ULDSEIN_TRAIN' => 'nullable|integer',
        'COAL_INCOME' => 'nullable|integer',
        'COAL_OUTCOME' => 'nullable|integer',
        'COAL_TRAIN_QUANTITY' => 'nullable|numeric',
        'COAL_REMAIN' => 'nullable|integer',
        'COAL_REMAIN_BYDAY' => 'nullable|numeric',
        'COAL_REMAIN_BYWINTERDAY' => 'nullable|integer',
        'MAZUT_INCOME' => 'nullable|integer',
        'MAZUT_OUTCOME' => 'nullable|integer',
        'MAZUT_TRAIN_QUANTITY' => 'nullable|integer',
        'MAZUT_REMAIN' => 'nullable|integer',
        'BAGANUUR_MINING_COAL_D' => 'nullable|integer',
        'SHARINGOL_MINING_COAL_D' => 'nullable|integer',
        'SHIVEEOVOO_MINING_COAL' => 'nullable|integer',
        'OTHER_MINIG_COAL_SUPPLY' => 'nullable|integer',
        'FUEL_SENDING_EMPL' => 'nullable|integer',
        'FUEL_RECEIVER_EMPL' => 'nullable|integer',
        'ORG_CODE' => 'required|integer',
        'ORG_NAME' => 'required|string|max:255',
        ]);

        DisCoal::create($request->all());

        return redirect()->route('dis_coal.index')
            ->with('success', 'Dis Coal record created successfully.');
    }


    public function show(string $id)
    {
        return view('dis_coal.show', compact('disCoal'));
    }

    public function edit(string $id)
    {
        return view('dis_coal.edit', compact('disCoal'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
        'date' => 'required|date',
        'CAME_TRAIN' => 'nullable|integer',
        'UNLOADING_TRAIN' => 'nullable|integer',
        'ULDSEIN_TRAIN' => 'nullable|integer',
        'COAL_INCOME' => 'nullable|integer',
        'COAL_OUTCOME' => 'nullable|integer',
        'COAL_TRAIN_QUANTITY' => 'nullable|numeric',
        'COAL_REMAIN' => 'nullable|integer',
        'COAL_REMAIN_BYDAY' => 'nullable|numeric',
        'COAL_REMAIN_BYWINTERDAY' => 'nullable|integer',
        'MAZUT_INCOME' => 'nullable|integer',
        'MAZUT_OUTCOME' => 'nullable|integer',
        'MAZUT_TRAIN_QUANTITY' => 'nullable|integer',
        'MAZUT_REMAIN' => 'nullable|integer',
        'BAGANUUR_MINING_COAL_D' => 'nullable|integer',
        'SHARINGOL_MINING_COAL_D' => 'nullable|integer',
        'SHIVEEOVOO_MINING_COAL' => 'nullable|integer',
        'OTHER_MINIG_COAL_SUPPLY' => 'nullable|integer',
        'FUEL_SENDING_EMPL' => 'nullable|integer',
        'FUEL_RECEIVER_EMPL' => 'nullable|integer',
        'ORG_CODE' => 'required|integer',
        'ORG_NAME' => 'required|string|max:255',
        ]);

        $disCoal = DisCoal::findOrFail($id);
        $disCoal->update($request->all());

        return redirect()->route('dis_coal.index')
            ->with('success', 'Dis Coal record updated successfully.');
    }

    public function destroy(string $id)
    {
        $disCoal = DisCoal::findOrFail($id);
        $disCoal->delete();

        return redirect()->route('dis_coal.index')
            ->with('success', 'Dis Coal record deleted successfully.');
    }
}
