<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class JetDemoSimpleSeeder extends Seeder
{
    /**
     * Seeder SIMPLE: hanya bikin akun user untuk testing login & role.
     *
     * - owner@jet.com        (role: owner, password: password)
     * - academic@jet.com     (role: academic_director, password: password)
     * - parent1@jet.com      (role: parent, password: password)
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'owner@jet.com'],
            [
                'name' => 'Owner JET',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );
 User::firstOrCreate(
            ['email' => 'coo@jet.com'],
            [
                'name' => 'Direktur Operasional',
                'password' => Hash::make('password'),
                'role' => 'operational_director',
            ]
        );

        User::firstOrCreate(
            ['email' => 'academic@jet.com'],
            [
                'name' => 'Direktur Akademik',
                'password' => Hash::make('password'),
                'role' => 'academic_director',
            ]
        );

        User::firstOrCreate(
            ['email' => 'parent1@jet.com'],
            [
                'name' => 'Orang Tua',
                'password' => Hash::make('password'),
                'role' => 'parent',
            ]
        );
    }
}
