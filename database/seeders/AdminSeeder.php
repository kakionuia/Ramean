<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Mencegah duplikasi jika dijalankan lagi
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'address' => 'Alamat Admin', // <--- Tambahkan baris ini (sesuaikan alamatnya)
            ]
        );
    }
}