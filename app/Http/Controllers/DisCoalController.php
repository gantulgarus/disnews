<?php

namespace App\Http\Controllers;

use App\Models\DisCoal;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\PowerPlantType;

class DisCoalController extends Controller
{

    public function index(Request $request)
    {
        $query = DisCoal::query();

        // Огноогоор фильтр хийх
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // Хэрэглэгчийн байгууллагын ID
        $userOrgId = auth()->user()->organization_id;

        // Хэрэв хэрэглэгч ДҮТ биш бол зөвхөн өөрийн байгууллагын бичлэг
        if ($userOrgId != 5) { // is_admin бол boolean талбар гэж үзсэн
            $query->where('organization_id', $userOrgId);
        } else {
            // ДҮТ бол organization_id-р фильтр хийж болно
            if ($request->filled('organization_id')) {
                $query->where('organization_id', $request->organization_id);
            }
        }

        $disCoals = $query->latest()->get();

        // 1. ДЦС төрлийг олж авна
        $powerPlantType = PowerPlantType::where('name', 'ДЦС')->first();

        if ($powerPlantType) {
            // 2. ДЦС төрлийн станцтай байгууллагуудыг distinct байдлаар гаргаж авна
            $organizations = Organization::whereHas('powerPlants', function ($query) use ($powerPlantType) {
                $query->where('power_plant_type_id', $powerPlantType->id);
            })->get();
        } else {
            $organizations = collect(); // хоосон коллекц
        }

        return view('dis_coal.index', compact('disCoals', 'organizations', 'userOrgId'));
    }


    // Show create form
    public function create()
    {
        $org_id = auth()->user()->organization_id;
        if ($org_id == 5) {
            $powerPlantType = PowerPlantType::where('name', 'ДЦС')->first();
            // 2. ДЦС төрлийн станцтай байгууллагуудыг distinct байдлаар гаргаж авна
            $organizations = Organization::whereHas('powerPlants', function ($query) use ($powerPlantType) {
                $query->where('power_plant_type_id', $powerPlantType->id);
            })->get();
        } else {
            $organizations = Organization::where('id', $org_id)->get();
        }

        return view('dis_coal.create', compact('organizations'));
    }

    // Store new record
    public function store(Request $request)
    {
        //dd($request->all());
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
            'organization_id' => 'nullable|exists:organizations,id',
        ]);

        $input = $request->all();

        // ORG_NAME-г автоматаар нэмэх
        $input['ORG_NAME'] = auth()->user()->organization->name ?? '';

        // organization_id-г request-аас шууд авна
        $input['organization_id'] = $request->organization_id;

        // COAL_REMAIN_BYDAY-г тооцоолох
        if (!empty($input['COAL_OUTCOME']) && $input['COAL_OUTCOME'] > 0) {
            $input['COAL_REMAIN_BYDAY'] = round($input['COAL_REMAIN'] / $input['COAL_OUTCOME'], 2);
        } else {
            $input['COAL_REMAIN_BYDAY'] = 0;
        }

        // Бүртгэх
        DisCoal::create($input);


        return redirect()->route('dis_coal.index')
            ->with('success', 'Амжилттай.');
    }

    // Show one record
    public function show(string $id)
    {
        $disCoal = DisCoal::findOrFail($id); // one record
        return view('dis_coal.show', compact('disCoal'));
    }


    public function edit(string $id)
    {
        $disCoal = DisCoal::findOrFail($id); // one record
        $org_id = auth()->user()->organization_id;
        if ($org_id == 5) {
            $powerPlantType = PowerPlantType::where('name', 'ДЦС')->first();
            // 2. ДЦС төрлийн станцтай байгууллагуудыг distinct байдлаар гаргаж авна
            $organizations = Organization::whereHas('powerPlants', function ($query) use ($powerPlantType) {
                $query->where('power_plant_type_id', $powerPlantType->id);
            })->get();
        } else {
            $organizations = Organization::where('id', $org_id)->get();
        }

        return view('dis_coal.edit', compact('disCoal', 'organizations'));
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
            'organization_id' => 'nullable|exists:organizations,id',
        ]);

        $input = $request->all();

        if (!empty($input['COAL_OUTCOME']) && $input['COAL_OUTCOME'] > 0) {
            $input['COAL_REMAIN_BYDAY'] = round($input['COAL_REMAIN'] / $input['COAL_OUTCOME'], 2);
        } else {
            $input['COAL_REMAIN_BYDAY'] = 0;
        }

        $disCoal = DisCoal::findOrFail($id);
        $disCoal->update($input);

        return redirect()->route('dis_coal.index')
            ->with('success', 'Амжилттай шинэчлэгдсэн..');
    }

    // Delete record
    public function destroy(string $id)
    {
        $disCoal = DisCoal::findOrFail($id); // one record
        $disCoal->delete();

        return redirect()->route('dis_coal.index')
            ->with('success', 'Амжилттай устгагдсан.');
    }
}
