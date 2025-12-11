<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->truncate();

        DB::table('roles')->insert([
            [
                'title'       => 'Admin',
                'description' => 'System administrator with full access',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Student',
                'description' => 'Learner with access to courses and materials',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Tutor',
                'description' => 'Instructor with course creation permissions',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
