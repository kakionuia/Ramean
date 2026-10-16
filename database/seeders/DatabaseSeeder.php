<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'address' => 'Belum diatur',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin_washly@gmail.com'],
            [
                'name' => 'Admin Washly',
                'address' => 'Belum diatur',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );
    }
}
