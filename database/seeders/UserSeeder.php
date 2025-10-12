<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat User Admin
        User::create([
            'name' => 'Admin SINDAPUR',
            'username' => 'admin',
            'password' => Hash::make('admin111'),
            'role_id' => 1
        ]);

        // Membuat User Staf Dapur
        User::create([
            'name' => 'Staf Dapur 1',
            'username' => 'staf',
            'password' => Hash::make('staf111'),
            'role_id' => 2 
        ]);
    }
}
