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
            'address'       => 'required|string|max:255',
            'domisili'      => 'required|string',
            'sub_domisili'  => 'required|string',
            'kecamatan'     => 'required|string',
            'wilayah_akhir' => 'required|string', // Desa atau Kelurahan terakhir
            'password'      => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
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
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $user = Auth::user();

            if ($user->role !== 'admin' && $user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => $user->status === 'pending'
                        ? 'Akun masih menunggu persetujuan admin.'
                        : 'Akun ini belum dapat digunakan. Hubungi admin.',
                ])->onlyInput('email');
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
            'email' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout!');
    }
}