<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi manual — hindari dependency translator Laravel
        if (empty($request->username) || empty($request->password)) {
            return back()->withErrors(['username' => 'Username dan password wajib diisi.'])->withInput();
        }

        $user = User::where('username', strtolower($request->username))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));

        // Set session tambahan untuk backward-compat middleware
        if ($user->role === 'pewarta') {
            session(['pewarta_user_id' => $user->id]);
        } elseif ($user->role === 'redaksi') {
            session(['redaksi_user_id' => $user->id]);
        }

        return $this->redirectByRole($user->role);
    }

    public function register(Request $request)
    {
        if (empty($request->username) || empty($request->display_name) || empty($request->email) || empty($request->password)) {
            return back()->withErrors(['username' => 'Semua field wajib diisi.'])->withInput();
        }
        if (strlen($request->username) < 3) {
            return back()->withErrors(['username' => 'Username minimal 3 karakter.'])->withInput();
        }
        if (strlen($request->password) < 6) {
            return back()->withErrors(['username' => 'Password minimal 6 karakter.'])->withInput();
        }
        if ($request->password !== $request->password_confirmation) {
            return back()->withErrors(['username' => 'Konfirmasi password tidak cocok.'])->withInput();
        }
        if (User::where('username', strtolower($request->username))->exists()) {
            return back()->withErrors(['username' => 'Username sudah digunakan.'])->withInput();
        }
        if (User::where('email', strtolower($request->email))->exists()) {
            return back()->withErrors(['email' => 'Email sudah terdaftar.'])->withInput();
        }

        $user = User::create([
            'username'     => strtolower($request->username),
            'display_name' => $request->display_name,
            'email'        => strtolower($request->email),
            'password'     => $request->password,
            'avatar_color' => User::pickColor($request->username),
            'role'         => 'reader',
        ]);

        Auth::login($user);
        return redirect('/')->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }

    public function showForgotForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.forgot-password');
    }

    public function verifyUsername(Request $request)
    {
        if (empty($request->username)) {
            return back()->withErrors(['username' => 'Masukkan username kamu.'])->withInput();
        }

        $user = User::where('username', strtolower(trim($request->username)))->first();

        if (!$user) {
            return back()->withErrors(['username' => 'Username tidak ditemukan.'])->withInput();
        }

        session(['reset_username' => $user->username]);
        return redirect()->route('password.reset');
    }

    public function showResetForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        if (!session('reset_username')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password', ['username' => session('reset_username')]);
    }

    public function resetPassword(Request $request)
    {
        $username = session('reset_username');
        if (!$username) {
            return redirect()->route('password.request');
        }
        if (empty($request->password) || empty($request->password_confirmation)) {
            return back()->withErrors(['password' => 'Semua field wajib diisi.'])->withInput();
        }
        if (strlen($request->password) < 6) {
            return back()->withErrors(['password' => 'Password minimal 6 karakter.'])->withInput();
        }
        if ($request->password !== $request->password_confirmation) {
            return back()->withErrors(['password' => 'Konfirmasi password tidak cocok.'])->withInput();
        }

        $user = User::where('username', $username)->first();
        if (!$user) {
            session()->forget('reset_username');
            return redirect()->route('password.request')->withErrors(['username' => 'Username tidak ditemukan.']);
        }

        $user->forceFill([
            'password' => $request->password,
        ])->save();

        session()->forget('reset_username');

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan masuk.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    private function redirectByRole(string $role): \Illuminate\Http\RedirectResponse
    {
        return match ($role) {
            'redaksi' => redirect()->route('redaksi.dashboard'),
            'pewarta' => redirect()->route('pewarta.dashboard'),
            default   => redirect('/'),
        };
    }
}
