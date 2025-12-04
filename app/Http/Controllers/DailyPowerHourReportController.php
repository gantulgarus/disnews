<?php

namespace App\Http\Controllers;

use App\Models\PowerPlant;
use Illuminate\Http\Request;
use App\Models\DailyPowerEquipment;
use App\Models\DailyPowerHourReport;
use Illuminate\Support\Facades\Auth;

class DailyPowerHourReportController extends Controller
{
    public function index(Request $request)
    {
        
          // хэрэглэгчийн байгууллагын бүх станц
            $plants = PowerPlant::where('organization_id', Auth::user()->organization_id)->get();

            // dd($plants);

            // станц сонгогдоогүй бол эхний станцыг default болгоно
            $plantId = $request->power_plant_id ?? ($plants->first()->id ?? null);

            if (!$plantId) {
                return back()->withErrors("Таны байгууллагад станц бүртгэгдээгүй байна.");
            }

               $date = $request->date ?? date('Y-m-d');

            // тухайн станцын бүх мэдээ
            $reports = DailyPowerHourReport::where('power_plant_id', $plantId)
                ->where('date', $date)
                ->orderBy('time', 'asc')
                ->get();

            // станцын бүх тоноглол
            $equipments = DailyPowerEquipment::where('power_plant_id', $plantId)->get();

            // Pivot
            $pivot = [];

                foreach ($reports as $r) {
                $t = $r->time;

                if (!isset($pivot[$t])) {
                $pivot[$t] = [
                    'id'   => $r->id,   // ← ID нэмэгдлээ
                    'time' => $t,
                    'date' => $r->date, 

                ];
                }

                $equipName = $r->equipment->power_equipment;
                $pivot[$t][$equipName] = $r->power_value;
                }

                $pivot = array_values($pivot);

            return view('daily_power_hour_reports.index', compact('plants', 'equipments', 'pivot', 'plantId', 'date'));
                    
        
    }




    public function create()
    {
        $user = Auth::user();
        $org_id = $user->organization_id;
        
        // Хэрэглэгчийн байгууллагын станцыг олно (нэг л ширхэг)
        $plant = PowerPlant::where('organization_id', $org_id)->first();

        if (!$plant) {
            return redirect()->back()->with('error', 'Таны байгууллагад станц бүртгэлгүй байна!');
        }

        // Тухайн станцын тоноглолуудыг авна
        $equipments = DailyPowerEquipment::where('power_plant_id', $plant->id)->get();

       // dd($equipments);

        return view('daily_power_hour_reports.create', compact('plant', 'equipments'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            'time' => 'required',
            'equipments' => 'required|array',
        ]);

        $userId = Auth::id();

        foreach ($request->equipments as $eq) {
            if (!empty($eq['power_value'])) {
                DailyPowerHourReport::create([
                    'power_plant_id' => $request->power_plant_id,
                    'daily_power_equipment_id' => $eq['id'],
                    'power_value' => $eq['power_value'],
                    'date' => $request->date,
                    'time' => $request->time,
                    'user_id' => $userId,
                ]);
            }
        }

        return redirect()->route('daily_power_hour_reports.index')
            ->with('success', 'Ачааллын цагийн мэдээг амжилттай хадгаллаа.');
    }



    public function editByTime($time)
        {
            $rows = DailyPowerHourReport::where('time', $time)->with('equipment')->get();

            if ($rows->isEmpty()) {
                return back()->withErrors("Тухайн цагийн бичлэг олдсонгүй.");
            }

            $first = $rows->first();

            $report = (object)[
                'time' => $time,
                'power_plant_id' => $first->power_plant_id
            ];

            $equipments = DailyPowerEquipment::where('power_plant_id', $first->power_plant_id)->get();

            // equipment_id => value
            $pivotValues = [];
            foreach ($rows as $r) {
                $pivotValues[$r->daily_power_equipment_id] = $r->power_value;
            }

            return view('daily_power_hour_reports.edit', compact('report', 'equipments', 'pivotValues'));
        }
        
     

public function updateByTime(Request $request, $time)
{
    $request->validate([
        'power_plant_id' => 'required|exists:power_plants,id',
        'date' => 'required|date',
        'equipments' => 'required|array',
        'equipments.*.id' => 'required|exists:daily_power_equipments,id',
        'equipments.*.power_value' => 'required',
    ]);

    foreach ($request->equipments as $eq) {
        $report = DailyPowerHourReport::where('time', $time)
            ->where('date', $request->date)
            ->where('daily_power_equipment_id', $eq['id'])
            ->first();

        if ($report) {
            $report->update([
                'power_value' => $eq['power_value']
            ]);
        }
    }


    return redirect()->route('daily_power_hour_reports.index')
                     ->with('success', 'Амжилттай шинэчиллээ');

}



public function userPowerReport(Request $request)
{
    $date = $request->input('date', date('Y-m-d'));

    // Цагууд
    $times = DailyPowerHourReport::where('date', $date)
                ->select('time')
                ->distinct()
                ->orderBy('time')
                ->pluck('time')
                ->toArray();

    // Хэрэглэгчид + станц + equipment-тай report-уудыг авна
    $reports = DailyPowerHourReport::with(['powerPlant','equipment'])
                ->where('date', $date)
                ->orderBy('power_plant_id')
                ->orderBy('daily_power_equipment_id')
                ->orderBy('time')
                ->get();

    // Pivot data бэлтгэх
    $data = [];
    foreach ($reports as $r) {
        $plantId = $r->power_plant_id;
        $equipId = $r->daily_power_equipment_id;

        $rowKey = $plantId . '_' . $equipId;

            if (!isset($data[$rowKey])) {
                $data[$rowKey] = [
                    'powerPlant' => $r->powerPlant,
                    'equipment' => $r->equipment,
                ];

                // Цагийн бүх баганыг эхлүүлж "-"
                foreach ($times as $time) {
                    $data[$rowKey][$time] = '-';
                }
            }
        // тухайн цагийн утгыг оруулах
         $data[$rowKey][$r->time] = $r->power_value;
    }

    return view('daily_power_hour_reports.report', compact('times', 'data', 'date'));
}


public function show($id)
{
    $report = DailyPowerHourReport::findOrFail($id);
    return view('daily_power_hour_reports.show', compact('report'));
}


}
