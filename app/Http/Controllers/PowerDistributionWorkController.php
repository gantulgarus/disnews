<?php

namespace App\Http\Controllers;

use App\Models\PowerDistributionWork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PowerDistributionWorkController extends Controller
{
    public function index()
    {
        $works = PowerDistributionWork::latest()->paginate(10);
        return view('power_distribution_works.index', compact('works'));
    }

    public function create()
    {
        return view('power_distribution_works.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tze' => 'required|string|max:255',
            'repair_work' => 'required|string|max:255',
            'description' => 'nullable|string',
            'restricted_energy' => 'nullable|numeric',
            'date' => 'required|date',
        ]);

        PowerDistributionWork::create([
            'tze' => $request->tze,
            'repair_work' => $request->repair_work,
            'description' => $request->description,
            'restricted_energy' => $request->restricted_energy,
            'date' => $request->date,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('power_distribution_works.index')
            ->with('success', 'Захиалгат ажил амжилттай нэмэгдлээ.');
    }

    public function show(PowerDistributionWork $powerDistributionWork)
    {
        return view('power_distribution_works.show', compact('powerDistributionWork'));
    }

    public function edit(PowerDistributionWork $powerDistributionWork)
    {
        return view('power_distribution_works.edit', compact('powerDistributionWork'));
    }

    public function update(Request $request, PowerDistributionWork $powerDistributionWork)
    {
        $request->validate([
            'tze' => 'required|string|max:255',
            'repair_work' => 'required|string|max:255',
            'description' => 'nullable|string',
            'restricted_energy' => 'nullable|numeric',
            'date' => 'required|date',
        ]);

        $powerDistributionWork->update($request->all());

        return redirect()->route('power_distribution_works.index')
            ->with('success', 'Захиалгат ажил амжилттай шинэчлэгдлээ.');
    }

    public function destroy(PowerDistributionWork $powerDistributionWork)
    {
        $powerDistributionWork->delete();
        return redirect()->route('power_distribution_works.index')
            ->with('success', 'Захиалгат ажил устгагдлаа.');
    }
}
