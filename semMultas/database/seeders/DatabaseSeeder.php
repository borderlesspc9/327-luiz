<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        //import o initialSeeder
        $this->call(RolesPermissionsSeeder::class);
        $this->call(ProcessFieldsSeeder::class);

        $userAdmin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@semmultas.com',
        ]);
        $userAdmin->assignRole('Admin');

    }
}
