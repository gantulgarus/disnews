<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\PermissionLevel;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller

{


    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }


    public function create()
    {

        $organizations = Organization::all(); // Fetch all organization records
        $permissions = PermissionLevel::all();
        $divisions = Division::all();

        return view('users.create', compact('organizations', 'permissions', 'divisions'));
        // return view('users.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'division_id' => 'required|exists:divisions,id',
            'permission_level_id' => 'nullable|string|exists:permission_levels,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'usercode' => 'required|string|min:6|max:255|unique:users,usercode', // min length 6
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


    public function edit(User $user)
    {
        $organizations = Organization::all();
        $permissions = PermissionLevel::all(); // Fetch all permission levels
        $divisions = Division::all();

        return view('users.edit', compact('user', 'organizations', 'permissions', 'divisions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'division_id' => 'required|exists:divisions,id',
            'permission_level_id' => 'nullable|string|exists:permission_levels,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'usercode' => 'required|string|min:6|max:255|unique:users,usercode,' . $user->id, // min length 6
            'phone' => 'nullable|string|max:8',
        ]);

        // dd($validated);

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

    // Хэрэглэгчийн профайл
    public function profile()
    {
        $user = auth()->user();
        return view('users.profile', compact('user'));
    }

    /**
     * Тусдаа нууц үг солих хуудас
     */
    public function editPassword(User $user)
    {
        return view('users.edit-password', compact('user'));
    }

    /**
     * Нууц үг шинэчлэх
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Нууц үг оруулна уу',
            'password.min' => 'Нууц үг дор хаяж 8 тэмдэгттэй байх ёстой',
            'password.confirmed' => 'Нууц үг таарахгүй байна',
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('users.index')->with('success', 'Нууц үг амжилттай солигдлоо.');
    }

    /**
     * Өөрийн профайлын нууц үг солих
     */
    public function updateOwnPassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Одоогийн нууц үгээ оруулна уу',
            'password.required' => 'Шинэ нууц үг оруулна уу',
            'password.min' => 'Нууц үг дор хаяж 8 тэмдэгттэй байх ёстой',
            'password.confirmed' => 'Нууц үг таарахгүй байна',
        ]);

        // Одоогийн нууц үг зөв эсэхийг шалгах
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Одоогийн нууц үг буруу байна']);
        }

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('success', 'Нууц үг амжилттай солигдлоо.');
    }
}