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
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'phone'         => 'required|string|max:20|unique:users',
            'address'       => 'required|string|max:255',
            'domisili'      => 'nullable|string|max:100',
            'sub_domisili'  => 'nullable|string|max:100',
            'kecamatan'     => 'nullable|string|max:100',
            'wilayah_akhir' => 'nullable|string|max:100',
            'password'      => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'address'       => $request->address,
            'domisili'      => $request->domisili,
            'sub_domisili'  => $request->sub_domisili,
            'kecamatan'     => $request->kecamatan,
            'wilayah_akhir' => $request->wilayah_akhir,
            'password'      => Hash::make($request->password),
        ]);

        // Otomatis login setelah registrasi
        Auth::login($user);

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

        $identityField = filter_var($credentials['identity'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $loginCredentials = [
            $identityField => $credentials['identity'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($loginCredentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->role !== 'admin' && $user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'identity' => $user->status === 'pending'
                        ? 'Akun masih menunggu persetujuan admin.'
                        : 'Akun ini belum dapat digunakan. Hubungi admin.',
                ])->onlyInput('identity');
            }

            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Berhasil login!');
            }

            if ($user->role === 'owner') {
                return redirect()->intended(route('owner.dashboard'))->with('success', 'Berhasil login!');
            }

            return redirect()->intended('/dashboard')->with('success', 'Berhasil login!');
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
