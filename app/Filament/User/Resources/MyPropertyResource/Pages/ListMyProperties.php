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
                ->tooltip(
                    ! $isKycApproved
                        ? 'You must complete KYC verification before listing a property'
                        : null
                )
                ->action(function () use ($isKycApproved) {
                    if (! $isKycApproved) {
                        Notification::make()
                            ->title('KYC Verification Required')
                            ->body('Please complete your KYC verification first to list properties.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->redirect(MyPropertyResource::getUrl('create'));
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Properties'),
            'approved' => Tab::make('Listed & Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('approval_status', 'approved'))
                ->badge(fn () => Property::where('user_id', Auth::id())->where('approval_status', 'approved')->count()),
            'pending' => Tab::make('Under Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('approval_status', 'pending'))
                ->badge(fn () => Property::where('user_id', Auth::id())->where('approval_status', 'pending')->count()),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('approval_status', 'rejected')),
        ];
    }
}
