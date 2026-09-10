<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Staff;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeReports extends Page
{
    protected string $view = 'filament.pages.employee-reports';

    protected static ?string $navigationLabel = 'Employee Reports';

    protected static ?string $title = 'Employee Reports';

    protected static ?int $navigationSort = 4;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-identification';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('staff.view');
    }

    public function totalStaff(): int
    {
        return Staff::count();
    }

    public function activeStaff(): int
    {
        return Staff::where('is_active', true)->count();
    }

    public function byDepartment(): array
    {
        return Staff::query()
            ->whereNotNull('department')
            ->select('department', DB::raw('count(*) as total'))
            ->groupBy('department')
            ->orderByDesc('total')
            ->pluck('total', 'department')
            ->all();
    }

    public function byEmploymentType(): array
    {
        return Staff::query()
            ->select('employment_type', DB::raw('count(*) as total'))
            ->groupBy('employment_type')
            ->pluck('total', 'employment_type')
            ->all();
    }

    public function attendanceThisMonth(): array
    {
        return Attendance::query()
            ->whereYear('attendance_date', now()->year)
            ->whereMonth('attendance_date', now()->month)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }

    public function leaveByStatus(): array
    {
        return LeaveRequest::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }
}
