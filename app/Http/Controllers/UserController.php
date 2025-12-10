<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Get all users grouped by role
     */
    public function index()
    {
        $users = User::all()->groupBy('role');

        return response()->json([
            'admins' => $users->get('admin', collect())->values(),
            'users' => $users->get('user', collect())->values(),
            'mentors' => $users->get('mentor', collect())->values(),
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,user,mentor'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return back()->with('success', ucfirst($request->role) . ' berhasil ditambahkan!');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting your own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus!');
    }
}
