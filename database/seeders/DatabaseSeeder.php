<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@folio.app'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => bcrypt('password'), // password: password
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
