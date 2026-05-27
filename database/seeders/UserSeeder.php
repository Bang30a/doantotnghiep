<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        
        // 1. Tạo tài khoản Admin
        User::create([
            'name' => 'Quản trị viên',
            'email' => 'admin@eduquiz.com',
            'password' => Hash::make('password'), 
            'role' => 'admin',
        ]);

        // 2. Tạo tài khoản Giảng viên
        User::create([
            'name' => 'Giảng viên Trần A',
            'email' => 'teacher@eduquiz.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // 3. Tạo tài khoản Học viên
        User::create([
            'name' => 'Học viên Nguyễn B',
            'email' => 'student@eduquiz.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);
    }
}