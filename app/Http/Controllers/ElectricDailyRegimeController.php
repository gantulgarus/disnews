<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PowerPlant;
use App\Models\ZConclusion;
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
        $isRegimeLead = $user->permissionLevel?->code === 'REGIME_LEAD';

        // Батлагдсан горимыг зөвхөн REGIME_LEAD засах эрхтэй
        if ($electricDailyRegime->status === 'approved' && !$isRegimeLead) {
            return redirect()->route('electric_daily_regimes.index')
                ->with('error', 'Батлагдсан горимыг засах эрх байхгүй байна.');
        }

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
        $user = Auth::user();
        $isRegimeLead = $user->permissionLevel?->code === 'REGIME_LEAD';

        // Батлагдсан горимыг зөвхөн REGIME_LEAD засах эрхтэй
        if ($electricDailyRegime->status === 'approved' && !$isRegimeLead) {
            return redirect()->route('electric_daily_regimes.index')
                ->with('error', 'Батлагдсан горимыг засах эрх байхгүй байна.');
        }

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
        $user = Auth::user();
        $isRegimeLead = $user->permissionLevel?->code === 'REGIME_LEAD';

        // Батлагдсан горимыг зөвхөн REGIME_LEAD устгах эрхтэй
        if ($electricDailyRegime->status === 'approved' && !$isRegimeLead) {
            return redirect()->route('electric_daily_regimes.index')
                ->with('error', 'Батлагдсан горимыг устгах эрх байхгүй байна.');
        }

        $electricDailyRegime->delete();

        return redirect()->route('electric_daily_regimes.index')
            ->with('success', 'Мэдээлэл амжилттай устгагдлаа.');
    }

    public function report(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        // Зөвхөн "ТБЭХС" бүсийн станцуудыг авна
        $powerPlants = PowerPlant::forDailyReport()->where('region', 'ТБЭХС')
            ->whereNot('power_plant_type_id', 6)
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
                $emptyRegime->power_plant_id = $plant->id;
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

        // ZConclusion-оос бодит гүйцэтгэлийг авах (Dashboard-тай адилхан логик)
        $dateCarbon = Carbon::parse($date);
        $startOfDay = $dateCarbon->copy()->startOfDay()->timestamp;
        $endOfDay = $dateCarbon->copy()->endOfDay()->timestamp;

        // Станцуудын short_name-уудыг авах
        $shortNames = $powerPlants->pluck('short_name')->filter()->toArray();

        // ZConclusion-оос тухайн өдрийн бодит өгөгдлийг цагаар группчилж авах
        $actualData = ZConclusion::selectRaw('VAR, HOUR(FROM_UNIXTIME(TIMESTAMP_S)) as hour_num, AVG(CAST(VALUE AS DECIMAL(10,2))) as avg_value')
            ->whereIn('VAR', $shortNames)
            ->whereBetween('TIMESTAMP_S', [$startOfDay, $endOfDay])
            ->where('CALCULATION', 50)
            ->groupBy('VAR', 'hour_num')
            ->get();

        // Станц бүрийн 24 цагийн бодит гүйцэтгэлийг бэлдэх
        $actualByPlant = [];
        foreach ($powerPlants as $plant) {
            // null утгаар эхлүүлэх (өгөгдөл байхгүй гэсэн үг)
            // 0-23 индекстэй массив (00:00 - 23:00)
            $hourlyActual = array_fill(0, 24, null);

            // Тухайн станцын өгөгдлийг шүүж авах
            $plantData = $actualData->where('VAR', $plant->short_name);

            foreach ($plantData as $record) {
                // hour_num 0-23 байна (00:00 - 23:00)
                $hour = $record->hour_num;
                if ($hour >= 0 && $hour <= 23) {
                    $hourlyActual[$hour] = round($record->avg_value, 2);
                }
            }

            $actualByPlant[$plant->id] = $hourlyActual;
        }

        return view('electric_daily_regimes.report', [
            'regimes' => $reportData,
            'date' => $date,
            'actualByPlant' => $actualByPlant
        ]);
    }

    public function approve(ElectricDailyRegime $electricDailyRegime)
    {
        $user = Auth::user();

        // Зөвхөн REGIME_LEAD эрхтэй хэрэглэгч батлах эрхтэй
        if ($user->permissionLevel?->code !== 'REGIME_LEAD') {
            return back()->with('error', 'Танд батлах эрх байхгүй байна.');
        }

        $electricDailyRegime->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Мэдээлэл амжилттай батлагдлаа.');
    }

    public function reject(ElectricDailyRegime $electricDailyRegime)
    {
        $user = Auth::user();

        // Зөвхөн REGIME_LEAD эрхтэй хэрэглэгч буцаах эрхтэй
        if ($user->permissionLevel?->code !== 'REGIME_LEAD') {
            return back()->with('error', 'Танд буцаах эрх байхгүй байна.');
        }

        $electricDailyRegime->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Мэдээлэл буцаагдлаа.');
    }
}