<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->truncate();

        // Get role IDs
        $adminRoleId = DB::table('roles')->where('title', 'Admin')->value('id');
        $studentRoleId = DB::table('roles')->where('title', 'Student')->value('id');
        $tutorRoleId = DB::table('roles')->where('title', 'Tutor')->value('id');

        DB::table('users')->insert([
            [
                'name'       => 'Super Admin',
                'email'      => 'admin@example.com',
                'role_id'    => $adminRoleId,
                'password'   => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Demo Student',
                'email'      => 'student@example.com',
                'role_id'    => $studentRoleId,
                'password'   => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Demo Tutor',
                'email'      => 'tutor@example.com',
                'role_id'    => $tutorRoleId,
                'password'   => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
