<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller

{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
         $users = User::all();
         return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
     
         $organizations = Organization::all(); // Fetch all organization records
         return view('users.create', compact('organizations'));
        // return view('users.create');
        
    }


    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:8',
            'password' => 'required|min:8'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Хэрэглэгч амжилттай бүртгэгдсэн.');

    }

    /**
     * Display the specified resource.
     */

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(User $user)
    {
        $organizations = Organization::all(); // Fetch all organization records
        return view('users.edit', compact('user', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
       $validated = $request->validate([
        'organization_id' => 'required|exists:organizations,id',
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'phone' => 'nullable|string|max:8',
    ]);

    $user->update($validated);

    return redirect()->route('users.index')->with('success', 'Хэрэглэгч амжилттай шинэчлэгдлээ.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Устсан');
    }
}
