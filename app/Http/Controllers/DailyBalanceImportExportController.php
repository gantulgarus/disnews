<?php

namespace App\Http\Controllers;

use App\Models\PowerPlant;
use Illuminate\Http\Request;
use App\Models\DailyBalanceImportExport;

class DailyBalanceImportExportController extends Controller
{
    public function index()
    {
        $userOrgId = auth()->user()->organization_id;

        $items = DailyBalanceImportExport::with('powerPlant')
            ->whereHas('powerPlant', function ($q) use ($userOrgId) {
                $q->where('organization_id', $userOrgId);
            })
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('daily_balance_import_exports.index', compact('items'));
    }

    public function create()
    {
        $userOrgId = auth()->user()->organization_id;

        $plants = PowerPlant::where('organization_id', $userOrgId)->get();

        return view('daily_balance_import_exports.create', compact('plants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            'import' => 'required|numeric',
            'export' => 'required|numeric',
        ]);

        DailyBalanceImportExport::create($request->all());

        return redirect()->route('daily-balance-import-exports.index')
            ->with('success', 'Импорт/Экспорт мэдээлэл амжилттай нэмэгдлээ.');
    }

    public function edit($id)
    {
        $userOrgId = auth()->user()->organization_id;

        $plants = PowerPlant::where('organization_id', $userOrgId)->get();

        $item = DailyBalanceImportExport::findOrFail($id);

        return view('daily_balance_import_exports.edit', compact('item', 'plants'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            'import' => 'required|numeric',
            'export' => 'required|numeric',
        ]);

        $item = DailyBalanceImportExport::findOrFail($id);
        $item->update($request->all());

        return redirect()->route('daily-balance-import-exports.index')
            ->with('success', 'Импорт/Экспорт мэдээлэл амжилттай засагдлаа.');
    }

    public function destroy($id)
    {
        DailyBalanceImportExport::findOrFail($id)->delete();

        return redirect()->route('daily-balance-import-exports.index')
            ->with('success', 'Мэдээлэл амжилттай устлаа.');
    }
}
