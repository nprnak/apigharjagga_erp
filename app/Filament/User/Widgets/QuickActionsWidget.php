<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class QuickActionsWidget extends Widget
{
    protected string $view = 'filament.user.widgets.quick-actions-widget';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
        'xl' => 2,
    ];

    public string $kycStatus = 'unsubmitted';

    public int $propertyCount = 0;

    public bool $canList = false;

    public function mount(): void
    {
        $user = Auth::user();
        $this->kycStatus = $user?->kycVerification?->status ?? 'unsubmitted';
        $this->propertyCount = $user?->properties()->count() ?? 0;
        $this->canList = $this->kycStatus === 'approved';
    }
}
