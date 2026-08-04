<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Controller untuk autentikasi terpusat (login, register, logout, reset password).
 *
 * Semua peran (reader, pewarta, redaksi) memakai satu halaman login di `/login`.
 * Setelah login, user diarahkan ke dashboard sesuai perannya.
 */
class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     *
     * Jika user sudah login, langsung diarahkan ke dashboard sesuai peran.
     *
     * @return View|RedirectResponse
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    /**
     * Memproses login dengan username + password.
     *
     * Validasi dilakukan manual untuk menghindari dependency translator.
     * Jika berhasil, session role lama (`pewarta_user_id` / `redaksi_user_id`)
     * tetap diisi untuk kompatibilitas middleware/test lama, lalu user
     * diarahkan ke dashboard sesuai perannya.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function login(Request $request)
    {
        if (empty($request->username) || empty($request->password)) {
            return back()->withErrors(['username' => 'Username dan password wajib diisi.'])->withInput();
        }

        $user = User::where('username', strtolower($request->username))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));

        // Backward-compat: session per-role untuk middleware & test lama.
        if ($user->role === 'pewarta') {
            session(['pewarta_user_id' => $user->id]);
        } elseif ($user->role === 'redaksi') {
            session(['redaksi_user_id' => $user->id]);
        }

        return $this->redirectByRole($user->role);
    }

    /**
     * Membuat akun pembaca baru (role `reader`).
     *
     * Field yang wajib: username (min 3 karakter & unik), display_name,
     * email (unik), dan password (min 6 karakter, harus cocok dengan
     * password_confirmation). Setelah terdaftar langsung login otomatis.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
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

    /**
     * Menampilkan form lupa password (input email).
     *
     * @return View|RedirectResponse
     */
    public function showForgotForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.forgot-password');
    }

    /**
     * Memverifikasi email milik user yang lupa password.
     *
     * Email disimpan ke session `reset_email` untuk dipakai pada langkah
     * berikutnya (form reset). Ini pengaman sederhana agar form reset hanya
     * bisa diakses setelah verifikasi email di tahap ini.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function verifyEmail(Request $request)
    {
        if (empty($request->email)) {
            return back()->withErrors(['email' => 'Masukkan email kamu.'])->withInput();
        }

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.'])->withInput();
        }

        session(['reset_email' => $user->email]);

        return redirect()->route('password.reset');
    }

    /**
     * Menampilkan form reset password.
     *
     * Hanya bisa diakses jika session `reset_email` tersedia (berasal dari
     * langkah verifikasi email). Jika tidak, diarahkan balik ke form lupa password.
     *
     * @return View|RedirectResponse
     */
    public function showResetForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        if (!session('reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password', ['email' => session('reset_email')]);
    }

    /**
     * Memperbarui password user setelah verifikasi email.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        $email = session('reset_email');

        if (!$email) {
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

        $user = User::where('email', $email)->first();

        if (!$user) {
            session()->forget('reset_email');

            return redirect()->route('password.request')->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        $user->forceFill([
            'password' => $request->password,
        ])->save();

        session()->forget('reset_email');

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan masuk.');
    }

    /**
     * Logout: mengakhiri sesi login dan membersihkan semua data session.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Mengarahkan user ke dashboard sesuai peran setelah login.
     *
     * @param string $role Role user ('redaksi', 'pewarta', atau lainnya).
     *
     * @return RedirectResponse
     */
    private function redirectByRole(string $role): RedirectResponse
    {
        return match ($role) {
            'redaksi' => redirect()->route('redaksi.dashboard'),
            'pewarta' => redirect()->route('pewarta.dashboard'),
            default   => redirect('/'),
        };
    }
}
