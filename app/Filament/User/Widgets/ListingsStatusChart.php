<?php

namespace App\Filament\User\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ListingsStatusChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Listing Status Breakdown';

    protected ?string $description = 'Distribution of your property submissions by approval status';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
        'xl' => 1,
    ];

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $userId = Auth::id();

        // Single aggregate query instead of 3 separate COUNTs.
        $counts = Property::where('user_id', $userId)
            ->selectRaw("count(case when approval_status = 'approved' then 1 end) as approved")
            ->selectRaw("count(case when approval_status = 'pending' then 1 end) as pending")
            ->selectRaw("count(case when approval_status = 'rejected' then 1 end) as rejected")
            ->first();

        return [
            'datasets' => [
                [
                    'label' => 'Listings',
                    'data' => [$counts->approved, $counts->pending, $counts->rejected],
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                        'rgba(239, 68, 68, 0.85)',
                    ],
                    'borderColor' => [
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => ['Approved', 'Pending Review', 'Rejected'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 16,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
            ],
            'cutout' => '68%',
            'maintainAspectRatio' => false,
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true,
                'duration' => 1200,
                'easing' => 'easeOutQuart',
            ],
        ];
    }
}
