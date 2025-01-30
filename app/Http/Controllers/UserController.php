<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'currentUser' => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'roles' => ['required', 'array'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles($validated['roles']);

        return redirect()->back();
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'roles' => ['required', 'array'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $user->syncRoles($validated['roles']);

        return redirect()->back();
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->back();
    }

    // 3.2.1 Reset user data
    public function resetUserData(User $user)
    {
        // Reset user's password to default
        $user->update([
            'password' => Hash::make('password123'),
        ]);

        // Revoke all tokens
        $user->tokens()->delete();

        // Reset two factor authentication
        if ($user->two_factor_secret) {
            $user->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
            ])->save();
        }

        return redirect()->back()->with('message', 'User data has been reset');
    }

    // 3.2.2 Disable user login
    public function toggleUserStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot disable your own account');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        // If disabling user, logout from all devices
        if (!$user->is_active) {
            $user->tokens()->delete();
            // Force logout from all devices
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect()->back()->with('message',
            $user->is_active ? 'User has been activated' : 'User has been disabled'
        );
    }
}
