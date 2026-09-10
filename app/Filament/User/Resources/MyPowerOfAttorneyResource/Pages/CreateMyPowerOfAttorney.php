<?php

namespace App\Filament\User\Resources\MyPowerOfAttorneyResource\Pages;

use App\Filament\User\Resources\MyPowerOfAttorneyResource;
use App\Models\Client;
use App\Models\PowerOfAttorney;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateMyPowerOfAttorney extends CreateRecord
{
    protected static string $resource = MyPowerOfAttorneyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $identifier = trim((string) ($data['owner_identifier'] ?? ''));

        $owner = Client::where('mobile_no', $identifier)
            ->orWhere('citizenship_no', $identifier)
            ->first();

        if (! $owner) {
            Notification::make()
                ->title('Owner not found')
                ->body("No registered client matches '{$identifier}'. Double-check the mobile number or citizenship number, or ask the owner to complete Annex-F registration first.")
                ->danger()
                ->send();

            $this->halt();
        }

        unset($data['owner_identifier']);
        $data['agent_user_id'] = Auth::id();
        $data['owner_client_id'] = $owner->client_id;
        $data['status'] = 'pending';

        return $data;
    }

    /**
     * One (agent, owner) pair is unique at the DB level, so resubmitting
     * after a rejection updates that same row back to pending instead of
     * hitting a duplicate-key error.
     */
    protected function handleRecordCreation(array $data): Model
    {
        return PowerOfAttorney::updateOrCreate(
            ['agent_user_id' => $data['agent_user_id'], 'owner_client_id' => $data['owner_client_id']],
            [
                'document_path' => $data['document_path'],
                'status' => 'pending',
                'verified_by_staff_id' => null,
                'verified_at' => null,
                'notes' => null,
            ],
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
