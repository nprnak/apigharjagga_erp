<?php

namespace App\Filament\User\Widgets;

use App\Models\KycVerification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class KycStatusWidget extends Widget
{
    protected string $view = 'filament.user.widgets.kyc-status-widget';

    protected int|string|array $columnSpan = 'full';

    public ?KycVerification $kyc = null;

    public function mount(): void
    {
        $this->kyc = Auth::user()?->kycVerification;
    }
}
