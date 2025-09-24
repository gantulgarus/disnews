<?php

namespace App\Http\Controllers;

use App\Models\Organization; 
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::all();
        return view('organizations.index', compact('organizations'));
    }

    public function create()
    {
        return view('organizations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:organizations,name',
            'org_code' => 'required|unique:organizations,org_code',
        ]);

        Organization::create($request->only('name', 'org_code'));

        return redirect()->route('organizations.index')
                         ->with('success', 'Амжилттай хадгаллаа.');
    }

    public function show(Organization $organization)
    {
        return view('organizations.show', compact('organization'));
    }

    public function edit(Organization $organization)
    {
        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'name' => 'required|unique:organizations,name,' . $organization->id,
            'org_code' => 'required|unique:organizations,org_code,' . $organization->id,
        ]);

        $organization->update($request->only('name', 'org_code'));

        return redirect()->route('organizations.index')
                         ->with('success', 'Амжилттай шинэчлэгдлээ.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->route('organizations.index')
                         ->with('success', 'Амжилттай устгалаа.');
    }
}