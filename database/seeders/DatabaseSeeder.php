<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@diskominfo.go.id'],
            [
                'name' => 'Admin Diskominfo',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
