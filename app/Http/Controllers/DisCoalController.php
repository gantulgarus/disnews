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
        if (!auth()->user()->hasPermission('dis_coal.view') && !auth()->user()->hasPermission('dis_coal.index')) {
            return redirect()->back()->with('error', 'Танд харах эрх байхгүй байна');
        }

        $user = auth()->user();
        $userOrgId = $user->organization_id;

        $query = DisCoal::query()->with('powerPlant');

        /* =========================
       ДҮТ (ӨДӨРӨӨР)
    ========================= */
        if ($userOrgId == 5) {

            $date = $request->filled('date')
                ? $request->date
                : now()->toDateString();

            $query->whereDate('date', $date);

            if ($request->filled('power_plant_id')) {
                $query->where('power_plant_id', $request->power_plant_id);
            }

            // Join хийхдээ зөв table нэр ашиглах
            $query->leftJoin('power_plants', 'dis_coal.power_plant_id', '=', 'power_plants.id')
                ->orderBy('power_plants.order', 'asc')
                ->select('dis_coal.*');  // dis_coals биш dis_coal

            $month = null;

            /* =========================
       СТАНЦ (САРААР)
    ========================= */
        } else {

            $month = $request->filled('month')
                ? $request->month
                : now()->format('Y-m');

            $query->whereYear('date', substr($month, 0, 4))
                ->whereMonth('date', substr($month, 5, 2));

            // зөвхөн өөрийн байгууллагын станц
            $query->whereHas('powerPlant', function ($q) use ($userOrgId) {
                $q->where('organization_id', $userOrgId);
            });

            $query->orderBy('date', 'asc');

            $date = null;
        }

        $disCoals = $query->get();

        // ДЦС жагсаалт (ДҮТ-д л хэрэгтэй)
        $powerPlantType = PowerPlantType::where('name', 'ДЦС')->first();
        $powerPlants = $userOrgId == 5 && $powerPlantType
            ? PowerPlant::where('power_plant_type_id', $powerPlantType->id)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get()
            : collect();

        return view('dis_coal.index', compact(
            'disCoals',
            'powerPlants',
            'userOrgId',
            'date',
            'month'
        ));
    }




    public function create()
    {
        if (!auth()->user()->hasPermission('dis_coal.create')) {
            return redirect()->back()->with('error', 'Танд энэ үйлдлийг хийх эрх байхгүй байна!');
        }

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

            // Вагон
            'CAME_TRAIN' => 'nullable|integer',
            'UNLOADING_TRAIN' => 'nullable|integer',
            'ULDSEIN_TRAIN' => 'nullable|integer',

            // Нүүрс (тонн) → numeric
            'COAL_INCOME' => 'nullable|numeric|min:0',
            'COAL_OUTCOME' => 'nullable|numeric|min:0',
            'COAL_REMAIN' => 'nullable|numeric|min:0',

            // Мазут (тонн)
            'MAZUT_INCOME' => 'nullable|numeric|min:0',
            'MAZUT_OUTCOME' => 'nullable|numeric|min:0',
            'MAZUT_REMAIN' => 'nullable|numeric|min:0',

            // Нийлүүлэлт (вагон)
            'BAGANUUR_MINING_COAL_D' => 'nullable|integer',
            'SHARINGOL_MINING_COAL_D' => 'nullable|integer',
            'SHIVEEOVOO_MINING_COAL' => 'nullable|integer',
            'OTHER_MINIG_COAL_SUPPLY' => 'nullable|integer',

            // Ажилтан
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

        // COAL_TRAIN_QUANTITY
        $input['COAL_TRAIN_QUANTITY'] = !empty($input['COAL_OUTCOME'])
            ? round($input['COAL_OUTCOME'] / 65, 2) // нэг вагонд дунджаар 65тн нүүрс адага
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
        $user = auth()->user();
        $disCoal = DisCoal::findOrFail($id);

        // Станцын хэрэглэгч + өнгөрсөн өдөр бол хориглоно
        if (
            $user->organization_id != 5 &&
            $disCoal->date < now()->toDateString()
        ) {
            return redirect()
                ->route('dis_coal.index')
                ->with('error', 'Өнгөрсөн өдрийн мэдээг засах боломжгүй');
        }

        if (!auth()->user()->hasPermission('dis_coal.edit')) {
            return redirect()->back()->with('error', 'Танд энэ үйлдлийг хийх эрх байхгүй байна!');
        }

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

        // COAL_TRAIN_QUANTITY
        $input['COAL_TRAIN_QUANTITY'] = !empty($input['COAL_OUTCOME'])
            ? round($input['COAL_OUTCOME'] / 65, 2) // нэг вагонд дунджаар 65тн нүүрс адага
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
        $user = auth()->user();
        $disCoal = DisCoal::findOrFail($id);

        if (
            $user->organization_id != 5 &&
            $disCoal->date < now()->toDateString()
        ) {
            return redirect()
                ->route('dis_coal.index')
                ->with('error', 'Өнгөрсөн өдрийн мэдээг устгах боломжгүй');
        }

        if (!auth()->user()->hasPermission('dis_coal.delete')) {
            return redirect()->back()->with('error', 'Танд энэ үйлдлийг хийх эрх байхгүй байна!');
        }

        DisCoal::findOrFail($id)->delete();

        return redirect()->route('dis_coal.index')
            ->with('success', 'Амжилттай устгагдсан.');
    }
}
