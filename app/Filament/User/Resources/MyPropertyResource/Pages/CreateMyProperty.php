<?php

namespace App\Filament\User\Resources\MyPropertyResource\Pages;

use App\Filament\User\Resources\MyPropertyResource;
use App\Models\Address;
use App\Models\Client;
use App\Models\PropertyListing;
use App\Models\PropertyPhoto;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateMyProperty extends CreateRecord
{
    protected static string $resource = MyPropertyResource::class;

    protected static ?string $title = 'Submit New Property Listing';

    protected function beforeCreate(): void
    {
        $user = Auth::user();
        if ($user?->kycVerification?->status !== 'approved') {
            Notification::make()
                ->title('KYC Approval Required')
                ->body('You must have an approved KYC verification before listing a property.')
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        // 1. Create Address
        $address = Address::create([
            'province' => $data['province'] ?? null,
            'district' => $data['district'] ?? null,
            'municipality' => $data['municipality'] ?? null,
            'ward_no' => $data['ward_no'] ?? null,
            'tole_locality' => $data['tole_locality'] ?? null,
        ]);

        // 2. Find or create Client
        $client = Client::query()->firstOrCreate(
            ['mobile_app_user_id' => (string) $user->id],
            [
                'client_code' => 'CLT-U'.$user->id,
                'client_type' => 'owner',
                'full_name' => $user->name,
                'email' => $user->email,
                'mobile_no' => $user->kycVerification?->mobile_no ?? '0000000000',
                'current_address_id' => $address->address_id,
                'registration_date' => now()->toDateString(),
                'mis_entry_status' => 'pending',
                'is_active' => true,
            ],
        );

        unset($data['property_photos']);

        $data['user_id'] = $user->id;
        $data['owner_client_id'] = $client->client_id;
        $data['address_id'] = $address->address_id;
        $data['property_code'] = 'PROP-'.strtoupper(Str::random(8));
        $data['status'] = 'draft';
        $data['approval_status'] = 'pending';

        return $data;
    }

    protected function afterCreate(): void
    {
        $property = $this->record;
        $formData = $this->form->getRawState();

        $sequence = PropertyListing::query()->count() + 1;

        $hasPhotos = ! empty($formData['property_photos']);

        $listing = PropertyListing::create([
            'application_no' => 'AGJ-'.date('Ymd').'-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'property_id' => $property->property_id,
            'applicant_client_id' => $property->owner_client_id,
            'purpose_of_listing' => $formData['purpose_of_listing'] ?? 'sale',
            'expected_selling_price' => $formData['expected_selling_price'] ?? null,
            'rental_amount' => $formData['rental_amount'] ?? null,
            'photographs_received' => $hasPhotos,
            'listing_status' => 'approved',
        ]);

        if ($hasPhotos) {
            $photos = is_array($formData['property_photos']) ? $formData['property_photos'] : [$formData['property_photos']];
            foreach ($photos as $index => $photoPath) {
                if ($photoPath) {
                    PropertyPhoto::create([
                        'property_id' => $property->property_id,
                        'source_type' => 'listing',
                        'source_id' => $listing->listing_id,
                        'photo_type' => $index === 0 ? 'front' : 'other',
                        'file_ref' => $photoPath,
                        'uploaded_at' => now(),
                    ]);
                }
            }
        }

        Notification::make()
            ->title('Property Listed Successfully')
            ->body("Property Reference: {$property->property_code}. It is currently under review by admin.")
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
