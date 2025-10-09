<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\StationThermoData;

class StationThermoDataController extends Controller
{
    public function index(Request $request)
    {
        $query = StationThermoData::query();

        // Огноо filter
        $date = $request->date ?? Carbon::today()->toDateString(); // request-аар огноо ирээгүй бол өнөөдрийн огноо
        $query->where('infodate', $date);

        $data = $query->orderBy('infodate', 'asc')
            ->orderBy('infotime', 'asc')
            ->paginate(24)
            ->withQueryString(); // paginate дотор query string хадгалах

        return view('station_thermo.index', compact('data', 'date'));
    }

    public function create()
    {
        return view('station_thermo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'infodate' => 'required|date',
            'infotime' => 'required|integer',
            // бусад талбаруудыг хүсэлтээс шаардлагатай бол validate хийнэ
        ]);

        StationThermoData::create($request->all());

        return redirect()->route('station_thermo.index')
            ->with('success', 'Мэдээлэл амжилттай хадгалагдлаа');
    }

    public function show($id)
    {
        abort(404);
    }

    public function edit($id)
    {
        $item = StationThermoData::findOrFail($id);
        return view('station_thermo.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'infodate' => 'required|date',
            'infotime' => 'required|integer',
            // бусад талбаруудыг validate хийнэ
        ]);

        $item = StationThermoData::findOrFail($id);
        $item->update($request->all());

        return redirect()->route('station_thermo.index')
            ->with('success', 'Мэдээлэл амжилттай шинэчилэгдлээ');
    }

    public function destroy($id)
    {
        $item = StationThermoData::findOrFail($id);
        $item->delete();

        return redirect()->route('station_thermo.index')
            ->with('success', 'Мэдээлэл амжилттай устгагдлаа');
    }

    public function news(Request $request)
    {
        $query = StationThermoData::query();

        // Огноо filter
        $date = $request->date ?? Carbon::today()->toDateString(); // request-аар огноо ирээгүй бол өнөөдрийн огноо
        $query->where('infodate', $date);

        $data = $query->orderBy('infodate', 'asc')
            ->orderBy('infotime', 'asc')
            ->paginate(24)
            ->withQueryString(); // paginate дотор query string хадгалах

        return view('station_thermo.news', compact('data', 'date'));
    }
}
