<?php

namespace App\Http\Controllers;

use App\Models\Laundry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterOwnerController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            // Validasi Owner
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',

            // Validasi Laundry
            'store_name' => 'required|string|max:255',
            'store_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Jalankan Database Transaction
        DB::beginTransaction();

        try {
            // Upload Logo jika ada
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('laundry_logos', 'public');
            }

            // A. Simpan Data User (Owner)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => Hash::make($request->password),
                'role' => 'owner',      // Set Role otomatis jadi Owner
                'status' => 'pending',    // Opsional: 'pending' jika butuh ACC admin, atau 'active' jika langsung aktif
            ]);

            // B. Simpan Data Laundry
            Laundry::create([
                'user_id' => $user->id,
                'store_name' => $request->store_name,
                'store_phone' => $request->store_phone,
                'address' => $request->address,
                'description' => $request->description,
                'logo' => $logoPath,
            ]);

            DB::commit();

            return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan menunggu verifikasi admin atau lakukan login.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Owner registration failed.', ['exception' => $e]);

            return back()->with('error', 'Terjadi kesalahan saat pendaftaran. Silakan coba kembali.')->withInput();
        }
    }
}
