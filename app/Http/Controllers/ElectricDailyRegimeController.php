<?php

namespace App\Http\Controllers;

use App\Models\PowerPlant;
use Illuminate\Http\Request;
use App\Models\ElectricDailyRegime;
use Illuminate\Support\Facades\Auth;

class ElectricDailyRegimeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userOrgId = $user->organization_id;

        // Query эхлүүлэх
        $query = ElectricDailyRegime::query()->orderBy('date', 'asc');

        // Хэрэв админ биш (organ. id != 5) бол зөвхөн өөрийн байгууллагын станцуудыг харуулах
        if ($userOrgId != 5) {
            $query->whereHas('powerPlant', function ($q) use ($userOrgId) {
                $q->where('organization_id', $userOrgId);
            });
        }

        // 📌 Сарын фильтр
        if ($request->filled('month')) {
            // month = 2025-02 гэх мэт
            $query->whereYear('date', substr($request->month, 0, 4))
                ->whereMonth('date', substr($request->month, 5, 2));
        }

        $regimes = $query->get();

        return view('electric_daily_regimes.index', compact('regimes'));
    }


    public function create()
    {
        $user = Auth::user();
        $userOrgId = $user->organization_id;

        if ($userOrgId == 5) {
            // Админ -> бүх станц
            $powerPlants = PowerPlant::all();
        } else {
            // Админ биш -> зөвхөн өөрийн байгууллагын станцууд
            $powerPlants = PowerPlant::where('organization_id', $userOrgId)->get();
        }

        return view('electric_daily_regimes.create', compact('powerPlants'));
    }


    public function store(Request $request)
    {
        $input = $request->all();
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            // Та бусад талбаруудыг хүсвэл validate хийх
        ]);

        $input['user_id'] = auth()->id(); // Одоогийн хэрэглэгчийн ID-г автоматаар оруулах

        ElectricDailyRegime::create($input);

        return redirect()->route('electric_daily_regimes.index')
            ->with('success', 'Мэдээлэл амжилттай нэмэгдлээ.');
    }

    public function show(ElectricDailyRegime $electricDailyRegime)
    {
        return view('electric_daily_regimes.show', compact('electricDailyRegime'));
    }

    public function edit(ElectricDailyRegime $electricDailyRegime)
    {
        $user = Auth::user();
        $userOrgId = $user->organization_id;

        if ($userOrgId == 5) {
            // Админ -> бүх станц
            $powerPlants = PowerPlant::all();
        } else {
            // Админ биш -> зөвхөн өөрийн байгууллагын станцууд
            $powerPlants = PowerPlant::where('organization_id', $userOrgId)->get();
        }

        return view('electric_daily_regimes.edit', compact('electricDailyRegime', 'powerPlants'));
    }

    public function update(Request $request, ElectricDailyRegime $electricDailyRegime)
    {
        $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
        ]);

        $electricDailyRegime->update($request->all());

        return redirect()->route('electric_daily_regimes.index')
            ->with('success', 'Мэдээлэл амжилттай шинэчлэгдлээ.');
    }

    public function destroy(ElectricDailyRegime $electricDailyRegime)
    {
        $electricDailyRegime->delete();

        return redirect()->route('electric_daily_regimes.index')
            ->with('success', 'Мэдээлэл амжилттай устгагдлаа.');
    }

    public function report(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        // Зөвхөн "ТБЭХС" бүсийн станцуудыг авна
        $powerPlants = PowerPlant::where('region', 'ТБЭХС')
            ->orderBy('Order')
            ->get();

        // Өдрийн горимыг авна
        $regimes = ElectricDailyRegime::whereDate('date', $date)->get()->keyBy('power_plant_id');

        // Бүх станцуудын мэдээлэлд нийлүүлэх, хоосон утга тохируулна
        $reportData = $powerPlants->map(function ($plant) use ($regimes, $date) {
            if ($regimes->has($plant->id)) {
                return $regimes->get($plant->id);
            } else {
                // Хоосон өгөгдөл үүсгэх
                $emptyRegime = new ElectricDailyRegime();
                $emptyRegime->powerPlant = $plant;
                $emptyRegime->technical_pmax = 0;
                $emptyRegime->technical_pmin = 0;
                $emptyRegime->pmax = 0;
                $emptyRegime->pmin = 0;
                for ($i = 1; $i <= 24; $i++) {
                    $emptyRegime->{'hour_' . $i} = 0;
                }
                $emptyRegime->total_mwh = 0;
                $emptyRegime->date = $date;
                return $emptyRegime;
            }
        });

        return view('electric_daily_regimes.report', [
            'regimes' => $reportData,
            'date' => $date
        ]);
    }
}
