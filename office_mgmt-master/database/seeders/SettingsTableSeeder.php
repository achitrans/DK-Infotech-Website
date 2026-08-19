<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'name' => 'ip_check',
                'value' => 'on',
                'possible_values' => (['on', 'off']),
            ],
            [
                'name' => 'ips',
                'value' => '',
                'possible_values' => ([]),
            ],
            [
                'name' => 'attendance_in_time',
                'value' => '9:00',
                'possible_values' => (['7:00', '8:00', '9:00', '10:00', '11:00', '12:00', '13:00']),
            ],
            [
                'name' => 'attendance_out_time',
                'value' => '18:00',
                'possible_values' => (['15:00','16:00','17:00', '18:00', '19:00', '20:00', '21:00']),
            ],
            [
                'name' => 'latitude',
                'value' => '',
                'possible_values' => ([]),
            ],
            [
                'name' => 'longitude',
                'value' => '',
                'possible_values' => ([]),
            ],
            [
                'name' => 'geo_location_radius',
                'value' => '20',
                'possible_values' => ([]),
            ],
            [
                'name' => 'geo_location_status',
                'value' => 'off',
                'possible_values' => (['on', 'off']),
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['name' => $setting['name']],
                $setting
            );
        }
    }
}
