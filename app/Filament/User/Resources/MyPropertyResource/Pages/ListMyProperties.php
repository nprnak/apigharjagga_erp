<?php

namespace App\Filament\User\Resources\MyPropertyResource\Pages;

use App\Filament\User\Resources\MyPropertyResource;
use App\Models\Property;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListMyProperties extends ListRecords
{
    protected static string $resource = MyPropertyResource::class;

    protected function getHeaderActions(): array
    {
        $isKycApproved = Auth::user()?->kycVerification?->status === 'approved';

        return [
            Actions\CreateAction::make()
                ->label('List New Property')
                ->icon('heroicon-m-plus')
                ->before(function ($action) use ($isKycApproved) {
                    if (! $isKycApproved) {
                        Notification::make()
                            ->title('KYC Verification Required')
                            ->body('You must have an approved KYC verification before listing a property on the platform.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }

    public function getTabs(): array
    {
        $userId = Auth::id();

        return [
            'all' => Tab::make('All Properties'),
            'approved' => Tab::make('Listed & Active')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('approval_status', 'approved'))
                ->badge(fn () => Property::where('user_id', $userId)->where('approval_status', 'approved')->count()),
            'pending' => Tab::make('Under Review')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('approval_status', 'pending'))
                ->badge(fn () => Property::where('user_id', $userId)->where('approval_status', 'pending')->count()),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('approval_status', 'rejected')),
        ];
    }
}
