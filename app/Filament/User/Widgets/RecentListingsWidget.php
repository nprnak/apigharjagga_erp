<?php

namespace App\Filament\User\Widgets;

use App\Models\Property;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RecentListingsWidget extends Widget
{
    protected string $view = 'filament.user.widgets.recent-listings-widget';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    /** @var Collection<int, Property> */
    public Collection $properties;

    public function mount(): void
    {
        $this->properties = Property::query()
            ->where('user_id', Auth::id())
            ->with('address:address_id,municipality,district')
            ->latest('property_id')
            ->limit(5)
            ->get();
    }
}
