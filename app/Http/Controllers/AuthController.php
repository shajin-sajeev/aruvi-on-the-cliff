<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->user()->update(['last_login_at' => now()]);

            if (! $request->user()->isAdmin()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['email' => 'Only resort administrators can access this panel.'])->onlyInput('email');
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'                 => ['nullable', 'string', 'max:40'],
            'password'              => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'admin_secret'          => ['required', 'string'],
        ]);

        // Simple secret key to prevent public registrations
        if ($data['admin_secret'] !== config('app.admin_register_secret', env('ADMIN_REGISTER_SECRET', 'aruvi@admin2024'))) {
            return back()->withErrors(['admin_secret' => 'Invalid admin registration key.'])->withInput();
        }

        $adminRole = Role::where('slug', 'super-admin')->first()
                  ?? Role::where('slug', 'admin')->first();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role_id'  => $adminRole?->id,
            'status'   => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')->with('success', 'Welcome, ' . $user->name . '! Your admin account has been created.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
