<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['Annual Leave', 18],
            ['Sick Leave', 12],
            ['Casual Leave', 7],
            ['Maternity/Paternity Leave', 60],
            ['Unpaid Leave', 0],
        ];

        foreach ($types as [$name, $days]) {
            DB::table('leave_types')->updateOrInsert(
                ['name' => $name],
                ['default_days_per_year' => $days]
            );
        }
    }
}
