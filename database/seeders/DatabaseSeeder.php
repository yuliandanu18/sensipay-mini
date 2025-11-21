<?php

namespace Database\Seeders;

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
        // Kalau ingin menambah user factory, pastikan role tidak required.
        // Jika tidak perlu, sebaiknya dihapus untuk mencegah data sampah.
        //
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Jalankan seeder demo Sensipay/Sensijet
        $this->call([
            \Database\Seeders\JetDemoSimpleSeeder::class,
        ]);
    }
}
