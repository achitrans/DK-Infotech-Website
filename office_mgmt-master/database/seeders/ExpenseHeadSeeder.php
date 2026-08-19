<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseHead;
use Illuminate\Support\Str;

class ExpenseHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $heads = [
            ['name' => 'Travel', 'description' => 'Travel and transportation expenses'],
            ['name' => 'Office Supplies', 'description' => 'Stationery and office consumables'],
            ['name' => 'Utilities', 'description' => 'Electricity, water, internet bills'],
            ['name' => 'Meals & Entertainment', 'description' => 'Client meetings and staff refreshments'],
            ['name' => 'Software & Subscriptions', 'description' => 'SaaS, licenses and subscriptions'],
            ['name' => 'Repairs & Maintenance', 'description' => 'Office repairs and maintenance'],
            ['name' => 'Training & Development', 'description' => 'Employee training and courses'],
            ['name' => 'Miscellaneous', 'description' => 'Other general expenses'],
        ];

        foreach ($heads as $head) {
            ExpenseHead::updateOrCreate([
                'name' => $head['name'],
            ], [
                'description' => $head['description'],
                'is_active' => true,
            ]);
        }
    }
}
