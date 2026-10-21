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
        Auth::guard('web')->login($user);
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
            $userQuery = User::query()->where('email', $identity);
        } else {
            $digits = preg_replace('/\D+/', '', $identity);
            $phoneCandidates = array_filter(array_unique([
                $identity,
                $digits,
                '+'.$digits,
                str_starts_with($digits, '0') ? '62'.substr($digits, 1) : null,
                str_starts_with($digits, '62') ? '0'.substr($digits, 2) : null,
            ]));
            $userQuery = User::query()->whereIn('phone', $phoneCandidates ?: [$identity]);
        }

        $user = $userQuery->first();
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'identity' => 'Email/nomor WhatsApp atau password tidak cocok dengan data kami.',
            ])->onlyInput('identity');
        }

        if ($user->role !== 'admin' && $user->status !== 'active' && ! ($user->role === 'owner' && $user->status === 'pending')) {
            return back()->withErrors([
                'identity' => $user->status === 'pending'
                    ? 'Akun masih menunggu persetujuan admin.'
                    : 'Akun ini belum dapat digunakan. Hubungi admin.',
            ])->onlyInput('identity');
        }

        $guard = match ($user->role) {
            'admin' => 'admin',
            'owner' => 'owner',
            default => 'web',
        };

        // Move legacy owner/admin sessions out of the customer guard on first login after the split.
        $legacyWebUser = Auth::guard('web')->user();
        if ($user->role !== 'user' && $legacyWebUser?->is($user)) {
            Auth::guard('web')->logout();
        }

        Auth::guard($guard)->login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard')->with('success', 'Berhasil login!'),
            'owner' => redirect()->route('owner.dashboard')->with('success', 'Berhasil login!'),
            default => redirect()->route('dashboard')->with('success', 'Berhasil login!'),
        };
    }

    // Log out only the selected role guard so other open tabs remain signed in.
    public function logout(Request $request)
    {
        return $this->logoutGuard($request, 'web');
    }

    public function logoutOwner(Request $request)
    {
        return $this->logoutGuard($request, 'owner');
    }

    public function logoutAdmin(Request $request)
    {
        return $this->logoutGuard($request, 'admin');
    }

    private function logoutGuard(Request $request, string $guard)
    {
        Auth::guard($guard)->logout();
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Berhasil logout!');
    }
}
