<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('type', 'admin')->first();

        Branch::create([
            'user_id' => $admin?->id,
            'display_name' => 'Main Branch',
            'legal_name' => 'Company',
            'gstin' => null,
            'pan' => null,
            'tan' => null,
            'mobile' => '9876543210',
            'whatsapp_number' => '9876543210',
            'email' => 'branches@example.com',
            'address' => 'Patna, Bihar',
            'code' => 'MAIN',
            'manager_name' => 'Kumar',
            'manager_phone' => '9876543210',
            'state' => 'Bihar',
            'city' => 'Patna',
            'pincode' => '800001',
            'is_active' => true,
        ]);
    }
}
