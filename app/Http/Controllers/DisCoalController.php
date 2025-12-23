<?php

namespace App\Http\Controllers;

use App\Models\DisCoal;
use App\Models\Organization;
use App\Models\PowerPlant;
use App\Models\PowerPlantType;
use Illuminate\Http\Request;

class DisCoalController extends Controller
{
    public function index(Request $request)
    {
        $query = DisCoal::query();

        // Огноогоор фильтр хийх
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $userOrgId = auth()->user()->organization_id;

        if ($userOrgId != 5) {
            // ДҮТ биш бол зөвхөн өөрийн байгууллагын ДЦС-ууд
            $query->whereHas('powerPlant', function ($q) use ($userOrgId) {
                $q->where('organization_id', $userOrgId);
            });
        } else {
            // ДҮТ бол power_plant_id-р фильтр хийх боломжтой
            if ($request->filled('power_plant_id')) {
                $query->where('power_plant_id', $request->power_plant_id);
            }
        }

        $disCoals = $query->latest()->get();

        // ДЦС төрлийн станцын жагсаалт (select-д харуулах)
        $powerPlantType = PowerPlantType::where('name', 'ДЦС')->first();
        if ($powerPlantType) {
            if ($userOrgId == 5) {
                $powerPlants = PowerPlant::where('power_plant_type_id', $powerPlantType->id)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->get();
            } else {
                $powerPlants = PowerPlant::where('organization_id', $userOrgId)
                    ->where('power_plant_type_id', $powerPlantType->id)
                    ->whereNull('parent_id')
                    ->get();
            }
        } else {
            $powerPlants = collect();
        }

        return view('dis_coal.index', compact('disCoals', 'powerPlants', 'userOrgId'));
    }


    public function create()
    {
        $orgId = auth()->user()->organization_id;
        $powerPlantType = PowerPlantType::where('name', 'ДЦС')->first();

        if ($orgId == 5) {
            $powerPlants = PowerPlant::where('power_plant_type_id', $powerPlantType->id)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get(); // үндсэн станц->get();
        } else {
            $powerPlants = PowerPlant::where('organization_id', $orgId)
                ->where('power_plant_type_id', $powerPlantType->id)
                ->whereNull('parent_id') // үндсэн станц
                ->get();
        }

        return view('dis_coal.create', compact('powerPlants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'power_plant_id' => 'required|exists:power_plants,id',

            'CAME_TRAIN' => 'nullable|integer',
            'UNLOADING_TRAIN' => 'nullable|integer',
            'ULDSEIN_TRAIN' => 'nullable|integer',
            'COAL_INCOME' => 'nullable|integer',
            'COAL_OUTCOME' => 'nullable|integer',
            'COAL_TRAIN_QUANTITY' => 'nullable|numeric',
            'COAL_REMAIN' => 'nullable|integer',
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
        ]);

        $input = $request->all();

        $powerPlant = PowerPlant::with('organization')->find($request->power_plant_id);
        $input['ORG_NAME'] = $powerPlant->organization->name ?? '';

        // COAL_REMAIN_BYDAY
        $input['COAL_REMAIN_BYDAY'] = (!empty($input['COAL_OUTCOME']) && $input['COAL_OUTCOME'] > 0)
            ? round($input['COAL_REMAIN'] / $input['COAL_OUTCOME'], 2)
            : 0;

        // COAL_REMAIN_BYWINTERDAY - станц тус бүрийн тогтмол ашиглан
        $coalConstant = $powerPlant->coal_constant ?? 1; // default 1
        $input['COAL_REMAIN_BYWINTERDAY'] = !empty($input['COAL_REMAIN'])
            ? round($input['COAL_REMAIN'] / $coalConstant, 2)
            : 0;

        DisCoal::create($input);

        return redirect()->route('dis_coal.index')->with('success', 'Амжилттай.');
    }

    public function edit(string $id)
    {
        $disCoal = DisCoal::findOrFail($id);

        $orgId = auth()->user()->organization_id;
        $powerPlantType = PowerPlantType::where('name', 'ДЦС')->first();

        if ($orgId == 5) {
            $powerPlants = PowerPlant::where('power_plant_type_id', $powerPlantType->id)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get(); // үндсэн станц->get();
        } else {
            $powerPlants = PowerPlant::where('organization_id', $orgId)
                ->where('power_plant_type_id', $powerPlantType->id)
                ->whereNull('parent_id') // үндсэн станц
                ->get();
        }

        return view('dis_coal.edit', compact('disCoal', 'powerPlants'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'date' => 'required|date',
            'power_plant_id' => 'required|exists:power_plants,id',
        ]);

        $input = $request->all();

        $powerPlant = PowerPlant::find($request->power_plant_id);

        $input['COAL_REMAIN_BYDAY'] = (!empty($input['COAL_OUTCOME']) && $input['COAL_OUTCOME'] > 0)
            ? round($input['COAL_REMAIN'] / $input['COAL_OUTCOME'], 2)
            : 0;

        $coalConstant = $powerPlant->coal_constant ?? 1;
        $input['COAL_REMAIN_BYWINTERDAY'] = !empty($input['COAL_REMAIN'])
            ? round($input['COAL_REMAIN'] / $coalConstant, 2)
            : 0;

        DisCoal::findOrFail($id)->update($input);

        return redirect()->route('dis_coal.index')
            ->with('success', 'Амжилттай шинэчлэгдсэн.');
    }

    public function destroy(string $id)
    {
        DisCoal::findOrFail($id)->delete();

        return redirect()->route('dis_coal.index')
            ->with('success', 'Амжилттай устгагдсан.');
    }
}