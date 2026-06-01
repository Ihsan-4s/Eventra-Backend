<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
    User::create([
        'name'     => 'Admin Eventra',
        'email'    => 'admin@eventra.com',
        'password' => Hash::make('12345678'),
        'role'     => 'admin',
    ]);

    // Organizer
    User::create([
        'name'     => 'Organizer Test',
        'email'    => 'organizer@eventra.com',
        'password' => Hash::make('12345678'),
        'role'     => 'organizer',
    ]);
    }
}
