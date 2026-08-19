<?php

namespace App\Filament\Resources\KycVerificationResource\Pages;

use App\Filament\Resources\KycVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListKycVerifications extends ListRecords
{
    protected static string $resource = KycVerificationResource::class;

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
            'pending'  => Tab::make('Pending')->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'pending'))->badge(fn () => \App\Models\KycVerification::where('status', 'pending')->count()),
            'approved' => Tab::make('Approved')->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'approved')),
            'rejected' => Tab::make('Rejected')->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'rejected')),
        ];
    }
}
