<?php

namespace App\Http\Controllers;

use App\Models\PermissionLevel;
use Illuminate\Http\Request;

class PermissionLevelController extends Controller
{
   
    public function index()
    {
        $levels = PermissionLevel::all();
        return view('permission_levels.index', compact('levels'));
    }

    public function create()
    {
        return view('permission_levels.create');
    }

    public function store(Request $request)
    {
         $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:5',
        ]);

        PermissionLevel::create($validated);

        return redirect()->route('permission_levels.index')
                         ->with('success', 'Амжилттай хадгаллаа.');
                         
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $permissionLevel = PermissionLevel::findOrFail($id);
        return view('permission_levels.edit', compact('permissionLevel'));
    }

    public function update(Request $request, string $id)
    {
        $permissionLevel = PermissionLevel::findOrFail($id); // ID-аар объект авах
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:5',
        ]);

        $permissionLevel->update($validated);

        return redirect()->route('permission_levels.index')
                         ->with('success', 'Амжилттай шинэчлэгдлээ.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permissionLevel = PermissionLevel::findOrFail($id); // ID-аар объект авах 
        $permissionLevel->delete();
        return redirect()->route('permission_levels.index')
                         ->with('success', 'Амжилттай устгалаа.');
    }
}
