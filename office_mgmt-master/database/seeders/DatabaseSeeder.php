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
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'department' => 'admin',
            'type' => 'admin',
        ]);
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'department' => 'development',
            'type' => 'employee',
        ]);

//        User::factory(10)->create();

        $this->call(SettingsTableSeeder::class);
        $this->call(ExpenseHeadSeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(IndianStateCitySeeder::class);
    }
}
