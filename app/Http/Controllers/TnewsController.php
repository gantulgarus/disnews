<?php

namespace App\Http\Controllers;

use App\Models\Tnews;
use Illuminate\Http\Request;



class TnewsController extends Controller

{
    public function index()
    {
        $Tnews = Tnews::all();
        return view('tnews.index', compact('Tnews'));
    }

    public function create()

    {
        return view('tnews.create');
    }


    public function edit($id)
    {
        $tnews = Tnews::findOrFail($id);
        return view('tnews.edit', compact('tnews'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'TZE' => 'required|string|max:255',
            'tasralt' => 'required|string',
            'ArgaHemjee' => 'nullable|string',
            'HyzErchim' => 'nullable|string',

        ]);

        Tnews::create($request->all());

        return redirect()->route('tnews.index')->with('success', 'Тасралтын мэдээ амжилттай хадгалагдлаа.');
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'TZE' => 'required|string',
            'tasralt' => 'required|string',
            'ArgaHemjee' => 'nullable|string',
            'HyzErchim' => 'nullable|string',
        ]);

        $tnews = Tnews::findOrFail($id);
        $tnews->update($validated);

        return redirect()->route('tnews.index')->with('success', 'Амжилттай шинэчлэгдлээ.');
    }

    public function show($id)
    {
        $tnews = Tnews::findOrFail($id);
        return view('tnews.show', compact('tnews'));
    }


    public function destroy($id)
    {
        $tnews = Tnews::findOrFail($id);
        $tnews->delete();

        return redirect()->route('tnews.index')->with('success', 'Амжилттай устгагдлаа');
    }
}
