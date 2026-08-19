<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all'      => Tab::make('All'),
            'pending'  => Tab::make('Pending')->modifyQueryUsing(fn (Builder $q) => $q->where('approval_status', 'pending'))->badge(fn () => \App\Models\Property::where('approval_status', 'pending')->count()),
            'approved' => Tab::make('Approved')->modifyQueryUsing(fn (Builder $q) => $q->where('approval_status', 'approved')),
            'rejected' => Tab::make('Rejected')->modifyQueryUsing(fn (Builder $q) => $q->where('approval_status', 'rejected')),
        ];
    }
}
