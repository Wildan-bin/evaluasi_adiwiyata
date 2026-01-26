<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view (for guest - default user).
     */
    public function create(): Response
    {
        Log::info('[Register] Guest accessing registration page');
        
        return Inertia::render('Auth/Register', [
            'role' => 'user',
            'isAdminCreating' => false,
        ]);
    }

    /**
     * ✅ Display the registration view with specific role (Admin only).
     */
    public function createWithRole(string $role): Response
    {
        // Validate role
        if (!in_array($role, ['admin', 'user', 'mentor'])) {
            Log::warning("[Register] Invalid role requested: {$role}, defaulting to 'user'");
            $role = 'user';
        }

        $currentUser = Auth::user();
        Log::info("[Register] Admin '{$currentUser->email}' accessing registration page for role: {$role}");

        return Inertia::render('Auth/Register', [
            'role' => $role,
            'isAdminCreating' => true,
        ]);
    }

    /**
     * Handle an incoming registration request (for guest - self registration).
     */
    public function store(Request $request): RedirectResponse
    {
        Log::info('[Register] Guest self-registration attempt', [
            'email' => $request->email,
            'name' => $request->name,
        ]);

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
            ]);

            Log::info('[Register] ✅ Guest registration SUCCESS', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect(route('dashboard', absolute: false));

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('[Register] ❌ Guest registration VALIDATION FAILED', [
                'email' => $request->email,
                'errors' => $e->errors(),
            ]);
            throw $e;

        } catch (\Exception $e) {
            Log::error('[Register] ❌ Guest registration ERROR', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * ✅ Handle registration by admin (can set any role).
     */
    public function storeByAdmin(Request $request, string $role): RedirectResponse
    {
        $currentAdmin = Auth::user();
        
        Log::info('[Register] Admin registration attempt', [
            'admin_id' => $currentAdmin->id,
            'admin_email' => $currentAdmin->email,
            'target_role' => $role,
            'target_email' => $request->email,
            'target_name' => $request->name,
        ]);

        // Validate role parameter
        if (!in_array($role, ['admin', 'user', 'mentor'])) {
            Log::warning("[Register] Invalid role: {$role}, defaulting to 'user'");
            // $role = 'user';
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $role,
                'email_verified_at' => now(),
            ]);

            Log::info('[Register] ✅ Admin registration SUCCESS', [
                'created_by_admin' => $currentAdmin->email,
                'new_user_id' => $user->id,
                'new_user_email' => $user->email,
                'new_user_name' => $user->name,
                'new_user_role' => $user->role,
                'email_verified' => $user->email_verified_at ? 'YES' : 'NO',
            ]);

            // Debug: Verify user was actually saved
            $savedUser = User::find($user->id);
            Log::info('[Register] Database verification', [
                'user_exists' => $savedUser ? 'YES' : 'NO',
                'saved_role' => $savedUser?->role,
                'saved_email' => $savedUser?->email,
            ]);

            event(new Registered($user));

            $roleDisplayName = [
                'admin' => 'Admin',
                'user' => 'User',
                'mentor' => 'Mentor',
            ][$role] ?? 'User';

            return redirect()->route('dashboard')
                ->with('success', "{$roleDisplayName} '{$user->name}' berhasil didaftarkan!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('[Register] ❌ Admin registration VALIDATION FAILED', [
                'admin_email' => $currentAdmin->email,
                'target_email' => $request->email,
                'target_role' => $role,
                'errors' => $e->errors(),
            ]);
            throw $e;

        } catch (\Exception $e) {
            Log::error('[Register] ❌ Admin registration ERROR', [
                'admin_email' => $currentAdmin->email,
                'target_email' => $request->email,
                'target_role' => $role,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
