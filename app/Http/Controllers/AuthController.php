<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showAdminPasswordForm(): View
    {
        return view('admin.password');
    }

    public function updateAdminPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.password.edit')->with('success', 'Password admin berhasil diperbarui.');
    }

    public function showStudentPasswordForm(): View
    {
        return view('auth.password');
    }

    public function updateStudentPassword(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kata sandi berhasil diperbarui.',
            ]);
        }

        return redirect()->route('pendaftaran.password.edit')->with('success', 'Kata sandi berhasil diperbarui.');
    }

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            return $user->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('pendaftaran.step1');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            // Cek apakah siswa sudah punya pendaftaran
            $calonSiswa = $user->calonSiswa;
            if ($calonSiswa && $calonSiswa->latestPendaftaran) {
                return redirect()->route('pendaftaran.card')->with('success', 'Selamat datang kembali.');
            }

            return redirect()->intended(route('pendaftaran.step1'))->with('success', 'Login berhasil! Silakan lengkapi formulir pendaftaran.');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('pendaftaran.step1');
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
            'no_whatsapp' => ['required', 'string', 'regex:/^\+?[0-9]+$/', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'no_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka dan tanda + di awal.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'siswa',
            ]);

            CalonSiswa::create([
                'id_user' => $user->id_user,
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_whatsapp' => $validated['no_whatsapp'],
            ]);

            DB::commit();

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('pendaftaran.step1')->with('success', 'Akun berhasil dibuat! Silakan lanjutkan ke pengisian data identitas.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['email' => 'Terjadi kesalahan saat pendaftaran akun: '.$e->getMessage()])->withInput();
        }
    }

    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flash('success', 'Anda telah berhasil logout.');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('login'),
                'message' => 'Anda telah berhasil logout.',
            ]);
        }

        return redirect()->route('login');
    }
}
