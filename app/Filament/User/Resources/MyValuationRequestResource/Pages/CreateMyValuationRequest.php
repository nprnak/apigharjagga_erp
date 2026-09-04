<?php

namespace App\Filament\User\Resources\MyValuationRequestResource\Pages;

use App\Filament\User\Resources\MyValuationRequestResource;
use App\Models\Property;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateMyValuationRequest extends CreateRecord
{
    protected static string $resource = MyValuationRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $property = Property::query()
            ->where('property_id', $data['property_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $data['client_id'] = $property->owner_client_id;
        $data['request_code'] = 'VAL-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        $data['application_received_date'] = now()->toDateString();
        $data['status'] = 'received';

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Valuation request submitted')
            ->body('Your request has been sent to the valuation team.')
            ->success();
    }
}
