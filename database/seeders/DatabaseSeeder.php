<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Master Account
        User::factory()->create([
            'name' => 'Master AFFA',
            'email' => 'master@affa.com',
            'password' => bcrypt('password'),
            'role' => 'master',
        ]);

        // Admin Account
        User::factory()->create([
            'name' => 'Admin AFFA',
            'email' => 'admin@affa.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Pelatih Account
        User::factory()->create([
            'name' => 'Coach AFFA',
            'email' => 'pelatih@affa.com',
            'password' => bcrypt('password'),
            'role' => 'pelatih',
        ]);

        // Murid Account
        User::factory()->create([
            'name' => 'Murid AFFA',
            'email' => 'murid@affa.com',
            'password' => bcrypt('password'),
            'role' => 'murid',
        ]);
    }
}
