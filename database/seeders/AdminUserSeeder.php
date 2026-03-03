<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@hr.local'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('admin12345'),
                'is_admin' => true,
            ]
        );
    }
}
