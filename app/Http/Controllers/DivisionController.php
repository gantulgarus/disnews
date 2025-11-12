<?php
namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $divisions = Division::all();
        return view('divisions.index', compact('divisions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('divisions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Div_name' => 'required|string|max:255',
            'Div_code' => 'required|string|max:50|unique:divisions,Div_code'
        ]);

        Division::create($request->only(['Div_name', 'Div_code']));

        return redirect()->route('divisions.index')
                         ->with('success','Амжилттай хадгаллаа.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $division = Division::findOrFail($id);
        return view('divisions.show', compact('division'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $division = Division::findOrFail($id);
        return view('divisions.edit', compact('division'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $division = Division::findOrFail($id);

        $request->validate([
            'Div_name' => 'required|string|max:255',
            'Div_code' => 'required|string|max:50|unique:divisions,Div_code,'.$division->id
        ]);

        $division->update($request->only(['Div_name', 'Div_code']));

        return redirect()->route('divisions.index')
                         ->with('success','Амжилттай шинэчлэгдлээ.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $division = Division::findOrFail($id);
        $division->delete();

        return redirect()->route('divisions.index')
                         ->with('success','Амжилттай устгалаа.');
    }
}
