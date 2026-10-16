<?php

namespace App\Http\Controllers;

use App\Models\Laundry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RegisterOwnerController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')],
            'domisili' => ['required', 'string', 'max:100'],
            'sub_domisili' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'wilayah_akhir' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'store_name' => ['required', 'string', 'max:255'],
            'store_phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $logoPath = null;
        try {
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('laundry-logos', 'public');
            }

            $user = DB::transaction(function () use ($validated, $logoPath) {
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
                    'role' => 'owner',
                    'status' => 'pending',
                ]);

                Laundry::create([
                    'user_id' => $user->id,
                    'store_name' => $validated['store_name'],
                    'store_phone' => $validated['store_phone'],
                    'address' => $validated['address'],
                    'description' => $validated['description'] ?? null,
                    'logo' => $logoPath,
                ]);

                return $user;
            });
        } catch (\Throwable $exception) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            Log::error('Owner registration failed.', ['exception' => $exception]);

            return back()->with('error', 'Terjadi kesalahan saat pendaftaran. Silakan coba kembali.')->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('owner.dashboard')->with('success', 'Pendaftaran mitra berhasil dikirim.');
    }
}
