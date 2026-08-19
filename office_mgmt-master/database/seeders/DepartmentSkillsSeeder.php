<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Human Resource' => [
                'Recruitment',
                'Payroll Management',
                'Employee Relations',
                'Attendance Management',
                'Compliance'
            ],

            'Development' => [
                'PHP',
                'Laravel',
                'JavaScript',
                'MySQL',
                'API Integration',
                'ReactJS',
                'VueJS',
                'Version Control (Git)',
                'NodeJS',
                'MongoDB',
            ],
            'Internship' => [
                'Php',
                'Mysql',
                'C Programming',
                'Laravel',
            ],

            'Digital Marketing' => [
                'SEO',
                'Social Media Marketing',
                'Google Ads',
                'Content Writing',
                'Analytics'
            ],
            'Sales' => [
                'Lead Generation',
                'Client Communication',
                'Negotiation',
                'CRM Handling',
                'Target Achievement'
            ],
            'Team Lead' => [
                'Project Management',
                'Team Coordination',
                'Agile Methodologies',
                'Conflict Resolution',
                'Performance Monitoring'
            ],

        ];

        foreach ($data as $department => $skills) {
            DB::table('department_skills')->insert([
                'department' => $department,
                'skills' => json_encode($skills),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
