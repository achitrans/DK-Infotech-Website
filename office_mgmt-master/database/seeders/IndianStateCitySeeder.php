<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\City;

class IndianStateCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('data/combined.json')), true);

        foreach ($data['states'] as $entry) {
            $state = State::create([
                'name' => $entry['name'],
                'code' => $entry['code'] ?? null,
            ]);

            foreach ($entry['cities'] as $cityName) {
                City::create([
                    'state_id' => $state->id,
                    'name' => $cityName,
                ]);
            }
        }
    }
}
