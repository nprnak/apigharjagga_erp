<?php

namespace App\Filament\Pages;

use App\Models\Property;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PropertyReports extends Page
{
    protected string $view = 'filament.pages.property-reports';

    protected static ?string $navigationLabel = 'Property Reports';

    protected static ?string $title = 'Property Reports';

    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-chart-pie';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('properties.view');
    }

    public function byStatus(): array
    {
        return Property::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->pluck('total', 'status')
            ->all();
    }

    public function byType(): array
    {
        return Property::query()
            ->select('property_type', DB::raw('count(*) as total'))
            ->groupBy('property_type')
            ->orderByDesc('total')
            ->pluck('total', 'property_type')
            ->all();
    }

    public function byApproval(): array
    {
        return Property::query()
            ->select('approval_status', DB::raw('count(*) as total'))
            ->groupBy('approval_status')
            ->pluck('total', 'approval_status')
            ->all();
    }

    /**
     * Listings created per month for the last 6 months.
     *
     * @return array<int, array{month: string, total: int}>
     */
    public function monthlyTrend(): array
    {
        $months = collect(range(5, 0))->map(fn (int $i) => now()->subMonths($i));

        return $months->map(function ($month) {
            $total = Property::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            return ['month' => $month->format('M Y'), 'total' => $total];
        })->all();
    }

    public function totalProperties(): int
    {
        return Property::count();
    }

    public function totalListed(): int
    {
        return Property::where('is_listed', true)->count();
    }
}
