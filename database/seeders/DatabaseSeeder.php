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

        User::factory()->create([
            'name' => 'admin koperasi',
            'email' => 'admin@gmail.com',
            'password' => Hash::make("admin123"),
            'role' => 'admin',
            'status' => 'active'
        ]);

        // Ketua Koperasi account
        User::factory()->create([
            'name' => 'ketua koperasi',
            'email' => 'ketua@gmail.com',
            'password' => Hash::make("ketua123"),
            'role' => 'ketua koperasi',
            'status' => 'active'
        ]);
    }
}
