<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $defaultBranchId = DB::table('branches')->orderBy('id')->value('id');

        foreach ($this->tables() as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($defaultBranchId) {
                if (! Schema::hasColumn($table->getTable(), 'branch_id')) {
                    $column = $table->foreignId('branch_id')->nullable();

                    if (! is_null($defaultBranchId)) {
                        $column->default($defaultBranchId);
                    }

                    $column->constrained('branches')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables() as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'branch_id')) {
                    $table->dropConstrainedForeignId('branch_id');
                }
            });
        }
    }

    /**
     * @return array<string>
     */
    private function tables(): array
    {
        return [
            'users',
            'estimates',
            'invoices',
            'expenses',
            'inquiries',
            'internship_interests',
            'leaves',
            'projects',
            'quotations',
            'user_salaries',
            'user_monthly_salaries',
            'attendances',
        ];
    }
};
