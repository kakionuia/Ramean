<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function showRegisterOwner()
    {
        return view('auth.register-owner');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'address' => 'required|string|max:255',
            'domisili' => 'required|string|max:100',
            'sub_domisili' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'wilayah_akhir' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'domisili' => $validated['domisili'],
            'sub_domisili' => $validated['sub_domisili'],
            'kecamatan' => $validated['kecamatan'],
            'wilayah_akhir' => $validated['wilayah_akhir'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'status' => 'active',
        ]);

        // Otomatis login setelah registrasi
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil!');
    }

    // Menampilkan Form Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses Otentikasi Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identity' => 'required|string',
            'password' => 'required',
        ]);

        $identity = trim($credentials['identity']);
        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $loginCredentials = ['email' => $identity, 'password' => $credentials['password']];
        } else {
            $digits = preg_replace('/\D+/', '', $identity);
            $phoneCandidates = array_filter(array_unique([
                $identity,
                $digits,
                '+' . $digits,
                str_starts_with($digits, '0') ? '62' . substr($digits, 1) : null,
                str_starts_with($digits, '62') ? '0' . substr($digits, 2) : null,
            ]));
            $storedPhone = User::query()->whereIn('phone', $phoneCandidates)->value('phone');
            $loginCredentials = [
                'phone' => $storedPhone ?? $identity,
                'password' => $credentials['password'],
            ];
        }

        if (Auth::attempt($loginCredentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->role !== 'admin' && $user->status !== 'active' && !($user->role === 'owner' && $user->status === 'pending')) {
                Auth::logout();

                return back()->withErrors([
                    'identity' => $user->status === 'pending'
                        ? 'Akun masih menunggu persetujuan admin.'
                        : 'Akun ini belum dapat digunakan. Hubungi admin.',
                ])->onlyInput('identity');
            }

            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil login!');
            }

            if ($user->role === 'owner') {
                return redirect()->route('owner.dashboard')->with('success', 'Berhasil login!');
            }

            return redirect()->route('dashboard')->with('success', 'Berhasil login!');
        }

        return back()->withErrors([
            'identity' => 'Email/nomor WhatsApp atau password tidak cocok dengan data kami.',
        ])->onlyInput('identity');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Berhasil logout!');
    }
}
