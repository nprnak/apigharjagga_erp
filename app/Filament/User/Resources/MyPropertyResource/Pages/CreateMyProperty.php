<?php

namespace App\Filament\User\Resources\MyPropertyResource\Pages;

use App\Filament\User\Resources\MyPropertyResource;
use App\Models\Address;
use App\Models\Client;
use App\Models\DocumentType;
use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\PropertyListing;
use App\Models\PropertyPhoto;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateMyProperty extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = MyPropertyResource::class;

    protected static ?string $title = 'Submit New Property Listing (Annex A)';

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user?->hasApprovedKyc()) {
            Notification::make()
                ->title('KYC Approval Required')
                ->body('You must have an approved KYC verification before listing a property.')
                ->danger()
                ->send();

            $this->redirect(MyPropertyResource::getUrl('index'));

            return;
        }

        parent::mount();
    }

    /**
     * @return array<int, Step>
     */
    protected function getSteps(): array
    {
        return [
            Step::make('Applicant Details')
                ->label('1. Applicant Details (आवेदक विवरण)')
                ->description('From your approved KYC record — nothing to fill in here')
                ->icon('heroicon-o-identification')
                ->completedIcon('heroicon-m-check')
                ->schema(MyPropertyResource::applicantDetailsFields()),

            Step::make('Property Owner')
                ->label('2. Property Owner Details (धनी विवरण)')
                ->description('Who owns this property')
                ->icon('heroicon-o-user')
                ->completedIcon('heroicon-m-check')
                ->columns(2)
                ->schema(MyPropertyResource::ownerDetailsFields()),

            Step::make('Property Details')
                ->label('3. Property Details (सम्पत्ति विवरण)')
                ->description('Type, address, land and building information')
                ->icon('heroicon-o-home-modern')
                ->completedIcon('heroicon-m-check')
                ->schema([
                    ...MyPropertyResource::propertyDetailsFields(),
                    Section::make('Address of Property (सम्पत्तिको ठेगाना)')
                        ->columns(2)
                        ->schema(MyPropertyResource::addressFields()),
                    Section::make('Land Information (जग्गा विवरण)')
                        ->columns(2)
                        ->schema(MyPropertyResource::landInformationFields()),
                    Section::make('Building Details — If Applicable (घर विवरण)')
                        ->columns(2)
                        ->visible(fn (Get $get) => in_array($get('property_type'), Property::BUILDING_TYPES, true))
                        ->schema(MyPropertyResource::buildingDetailsFields()),
                ]),

            Step::make('Purpose of Listing')
                ->label('4. Purpose of Listing (उद्देश्य)')
                ->icon('heroicon-o-tag')
                ->completedIcon('heroicon-m-check')
                ->schema(MyPropertyResource::purposeFields()),

            Step::make('Expected Price')
                ->label('5. Expected Price (अपेक्षित मूल्य)')
                ->icon('heroicon-o-banknotes')
                ->completedIcon('heroicon-m-check')
                ->columns(2)
                ->schema(MyPropertyResource::priceFields()),

            Step::make('Documents')
                ->label('6. Property Documents Submitted (कागजात)')
                ->description('Excludes documents already verified with your KYC')
                ->icon('heroicon-o-document-check')
                ->completedIcon('heroicon-m-check')
                ->columns(2)
                ->schema(MyPropertyResource::documentFields()),

            Step::make('Features')
                ->label('7. Property Features (विशेषताहरू)')
                ->icon('heroicon-o-sparkles')
                ->completedIcon('heroicon-m-check')
                ->schema(MyPropertyResource::featureFields()),

            Step::make('Photos')
                ->label('8. Photographs of Property (तस्विरहरू)')
                ->icon('heroicon-o-camera')
                ->completedIcon('heroicon-m-check')
                ->schema(MyPropertyResource::mediaFields()),

            Step::make('Declaration')
                ->label('9. Declaration & Signature (घोषणा)')
                ->icon('heroicon-o-pencil-square')
                ->completedIcon('heroicon-m-check')
                ->schema(MyPropertyResource::declarationFields()),
        ];
    }

    protected function beforeCreate(): void
    {
        $user = Auth::user();
        if (! $user?->hasApprovedKyc()) {
            Notification::make()
                ->title('KYC Approval Required')
                ->body('Please complete the KYC to list the property.')
                ->danger()
                ->send();

            $this->halt();
        }
    }

    /**
     * A sequential, collision-free property_code / application_no — the
     * previous convention (a random suffix / a raw count()+1) either
     * couldn't be checked for uniqueness or raced under concurrent
     * submissions. Mirrors the retry-until-unique pattern already used by
     * KycVerification::generateDigitalClientId().
     */
    public static function generatePropertyCode(): string
    {
        do {
            $candidate = 'PROP-'.strtoupper(Str::random(8));
        } while (Property::where('property_code', $candidate)->exists());

        return $candidate;
    }

    public static function generateApplicationNo(): string
    {
        $prefix = 'AGJ-'.date('Ymd').'-';
        do {
            $candidate = $prefix.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (PropertyListing::where('application_no', $candidate)->exists());

        return $candidate;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $kyc = $user->kycVerification;

        // 1. Create Address
        $address = Address::create([
            'province' => $data['province'] ?? null,
            'district' => $data['district'] ?? null,
            'municipality' => $data['municipality'] ?? null,
            'ward_no' => $data['ward_no'] ?? null,
            'tole_locality' => $data['tole_locality'] ?? null,
        ]);

        // 2. Find or create the owner's Client record, seeded from the
        // applicant's own KYC data rather than placeholder values.
        $client = Client::query()->firstOrCreate(
            ['mobile_app_user_id' => (string) $user->id],
            [
                'client_code' => 'CLT-U'.$user->id,
                'client_type' => 'owner',
                'full_name' => $kyc?->full_name ?? $user->name,
                'citizenship_no' => $kyc?->citizenship_no,
                'date_of_birth' => $kyc?->date_of_birth,
                'gender' => $kyc?->gender,
                'nationality' => $kyc?->nationality ?? 'Nepali',
                'occupation' => $kyc?->occupation,
                'email' => $kyc?->email ?? $user->email,
                'mobile_no' => $kyc?->mobile_no ?? '0000000000',
                'current_address_id' => $address->address_id,
                'registration_date' => now()->toDateString(),
                'mis_entry_status' => 'pending',
                'is_active' => true,
            ],
        );

        // The wizard-only fields (photos, documents, features, signature,
        // pricing) are read back from the raw form state in afterCreate()
        // instead — stripped from $data here so Property::create() below
        // doesn't choke on attributes it has no column for.
        foreach ([
            'province', 'district', 'municipality', 'ward_no', 'tole_locality',
            'property_photos', 'purpose_of_listing', 'expected_selling_price',
            'negotiable', 'minimum_acceptable_price', 'rental_amount',
            'feature_ids', 'applicant_signature_path', 'declaration_accepted',
            ...array_keys(MyPropertyResource::documentFieldMap()),
        ] as $key) {
            unset($data[$key]);
        }

        $data['user_id'] = $user->id;
        $data['owner_client_id'] = $client->client_id;
        $data['address_id'] = $address->address_id;
        $data['property_code'] = static::generatePropertyCode();
        $data['status'] = 'under_verification';
        $data['approval_status'] = 'pending';

        if ($data['ownership_role'] === 'self') {
            $data['owner_full_name'] = null;
            $data['owner_citizenship_no'] = null;
            $data['owner_relation'] = null;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $property = $this->record;
        $formData = $this->form->getRawState();

        $photos = is_array($formData['property_photos'] ?? null) ? $formData['property_photos'] : array_filter([$formData['property_photos'] ?? null]);
        $featureIds = $formData['feature_ids'] ?? [];

        DB::transaction(function () use ($property, $formData, $photos, $featureIds) {
            $listing = PropertyListing::create([
                'application_no' => static::generateApplicationNo(),
                'property_id' => $property->property_id,
                'applicant_client_id' => $property->owner_client_id,
                'purpose_of_listing' => $formData['purpose_of_listing'] ?? 'sale',
                'expected_selling_price' => $formData['expected_selling_price'] ?? null,
                'negotiable' => (bool) ($formData['negotiable'] ?? false),
                'minimum_acceptable_price' => $formData['minimum_acceptable_price'] ?? null,
                'rental_amount' => $formData['rental_amount'] ?? null,
                'photographs_received' => ! empty($photos),
                'listing_status' => 'pending',
                'applicant_signature_path' => $formData['applicant_signature_path'] ?? null,
            ]);

            // Photos
            foreach (array_values($photos) as $index => $photoPath) {
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

            // Document checklist
            foreach (MyPropertyResource::documentFieldMap() as $field => $docName) {
                $fileRef = $formData[$field] ?? null;
                $docType = DocumentType::firstOrCreate(['doc_name' => $docName], ['category' => 'land']);

                PropertyDocument::updateOrCreate(
                    ['property_id' => $property->property_id, 'doc_type_id' => $docType->doc_type_id],
                    ['file_ref' => $fileRef, 'status' => $fileRef ? 'submitted' : 'pending', 'updated_at' => now()],
                );
            }

            // Features
            if (! empty($featureIds)) {
                $property->features()->sync($featureIds);
            }
        });

        Notification::make()
            ->title('Property Listing Submitted Successfully')
            ->body("Property Reference: {$property->property_code}. It is now pending admin review.")
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
