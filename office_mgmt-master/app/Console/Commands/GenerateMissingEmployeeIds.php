<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GenerateMissingEmployeeIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-missing-employee-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing employee_id for users of type employee or intern';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Finding missing employee_ids...');

        $users = User::whereNull('employee_id')
            ->whereIn('type', ['employee', 'intern'])
            ->orderBy('id', 'asc')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No users found with missing employee_id.');
            return;
        }

        $empPrefix = env('EMPLOYEE_ID_PREFIX', 'EMP');
        $intPrefix = env('INTERN_ID_PREFIX', 'INT');

        // Find max existing employee ID purely in PHP
        $empMax = User::where('type', 'employee')
            ->whereNotNull('employee_id')
            ->pluck('employee_id')
            ->map(function ($id) use ($empPrefix) {
                return (int) substr($id, strlen($empPrefix));
            })->max() ?? 0;

        // Find max existing intern ID purely in PHP
        $intMax = User::where('type', 'intern')
            ->whereNotNull('employee_id')
            ->pluck('employee_id')
            ->map(function ($id) use ($intPrefix) {
                return (int) substr($id, strlen($intPrefix));
            })->max() ?? 0;

        $bar = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            if ($user->type === 'employee') {
                $empMax++;
                $user->employee_id = $empPrefix . str_pad($empMax, 4, '0', STR_PAD_LEFT);
            } elseif ($user->type === 'intern') {
                $intMax++;
                $user->employee_id = $intPrefix . str_pad($intMax, 4, '0', STR_PAD_LEFT);
            }

            $user->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Successfully generated missing employee_ids!');
    }
}
