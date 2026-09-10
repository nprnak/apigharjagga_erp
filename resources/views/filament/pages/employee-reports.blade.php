<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @include('filament.pages.partials.report-stat', ['label' => 'Total Staff', 'value' => $this->totalStaff()])
            @include('filament.pages.partials.report-stat', ['label' => 'Active Staff', 'value' => $this->activeStaff()])
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @include('filament.pages.partials.report-breakdown', ['title' => 'By Department', 'data' => $this->byDepartment()])
            @include('filament.pages.partials.report-breakdown', ['title' => 'By Employment Type', 'data' => $this->byEmploymentType()])
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @include('filament.pages.partials.report-breakdown', [
                'title' => 'Attendance This Month',
                'data' => $this->attendanceThisMonth(),
                'labelFormatter' => [
                    'present' => 'Present', 'absent' => 'Absent', 'half_day' => 'Half Day',
                    'on_leave' => 'On Leave', 'holiday' => 'Holiday',
                ],
            ])
            @include('filament.pages.partials.report-breakdown', [
                'title' => 'Leave Requests by Status',
                'data' => $this->leaveByStatus(),
                'labelFormatter' => ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'],
            ])
        </div>
    </div>
</x-filament-panels::page>
