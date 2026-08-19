<?php

namespace App\Filament\User\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class UserStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $userId = Auth::id();

        $activeListings = Property::where('user_id', $userId)
            ->where('approval_status', 'approved')
            ->count();

        $pendingListings = Property::where('user_id', $userId)
            ->where('approval_status', 'pending')
            ->count();

        $rejectedListings = Property::where('user_id', $userId)
            ->where('approval_status', 'rejected')
            ->count();

        $totalProperties = Property::where('user_id', $userId)->count();

        $kycStatus = Auth::user()?->kycVerification?->status ?? 'unsubmitted';

        $kycLabel = match ($kycStatus) {
            'approved' => 'Verified',
            'pending' => 'Under Review',
            'rejected' => 'Action Needed',
            default => 'Not Submitted',
        };

        $kycColor = match ($kycStatus) {
            'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            default => 'gray',
        };

        $kycChart = match ($kycStatus) {
            'approved' => [1, 2, 2, 3, 3, 4, 4],
            'pending' => [0, 1, 1, 2, 2, 2, 3],
            'rejected' => [2, 2, 1, 1, 0, 1, 0],
            default => [0, 0, 1, 1, 1, 2, 2],
        };

        return [
            Stat::make('KYC Identity Status', $kycLabel)
                ->description($kycStatus === 'approved' ? 'Authorized to list properties' : 'Verification required to list')
                ->descriptionIcon($kycStatus === 'approved' ? 'heroicon-m-shield-check' : 'heroicon-m-exclamation-circle')
                ->icon('heroicon-o-identification')
                ->chart($kycChart)
                ->color($kycColor)
                ->url(url('/dashboard/kyc-verification-page')),

            Stat::make('Active Listed Properties', $activeListings)
                ->description('Publicly visible on marketplace')
                ->descriptionIcon('heroicon-m-check-circle')
                ->icon('heroicon-o-building-office-2')
                ->chart($this->sparkline($activeListings))
                ->color('success')
                ->url(url('/dashboard/my-properties')),

            Stat::make('Pending Review', $pendingListings)
                ->description('Under verification by admin')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-clock')
                ->chart($this->sparkline($pendingListings))
                ->color($pendingListings > 0 ? 'warning' : 'gray')
                ->url(url('/dashboard/my-properties')),

            Stat::make('Total Submissions', $totalProperties)
                ->description($rejectedListings.' rejected · lifetime records')
                ->descriptionIcon('heroicon-m-home-modern')
                ->icon('heroicon-o-home-modern')
                ->chart($this->submissionTrend($userId))
                ->color('info')
                ->url(url('/dashboard/my-properties')),
        ];
    }

    /**
     * @return array<int, int>
     */
    private function sparkline(int $value): array
    {
        $base = max($value, 1);

        return [
            (int) max(0, $base - 3),
            (int) max(0, $base - 2),
            (int) max(0, $base - 1),
            $value,
            $value,
            $value,
            $value,
        ];
    }

    /**
     * @return array<int, int>
     */
    private function submissionTrend(int $userId): array
    {
        $trend = [];

        for ($i = 6; $i >= 0; $i--) {
            $trend[] = Property::query()
                ->where('user_id', $userId)
                ->whereDate('created_at', now()->subDays($i))
                ->count();
        }

        return $trend;
    }
}
