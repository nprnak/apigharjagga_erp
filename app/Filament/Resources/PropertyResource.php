<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'property_code';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-home-modern';
    }

    public static function getNavigationGroup(): string|null
    {
        return 'Properties';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('approval_status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        // Two-column workspace on large screens: an editable main column and a
        // read-only context sidebar (owner / location / record meta). Both
        // collapse to a single stacked column below `lg`.
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                // Review & visibility come first — this is what an admin opens
                // the record for, so it spans the full width above the fold.
                Section::make('Review & Visibility')
                    ->description('Control approval state and whether this property appears on the public marketplace.')
                    ->icon('heroicon-o-shield-check')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('approval_status')
                            ->label('Approval Status')
                            ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                            ->required()
                            ->native(false),
                        Select::make('status')
                            ->label('Property Status')
                            ->options([
                                'draft' => 'Draft', 'listed' => 'Listed', 'under_verification' => 'Under Verification',
                                'under_valuation' => 'Under Valuation', 'under_negotiation' => 'Under Negotiation',
                                'sold' => 'Sold', 'rented' => 'Rented', 'leased' => 'Leased',
                                'withdrawn' => 'Withdrawn', 'rejected' => 'Rejected',
                            ])
                            ->native(false),
                        Toggle::make('is_listed')
                            ->label('Show on Marketplace')
                            ->helperText('When off, this property is hidden from the public site and user listings.')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),

                // ---- Main editable column ---------------------------------
                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make('Basic Information')
                            ->icon('heroicon-o-home-modern')
                            ->columns(2)
                            ->schema([
                                TextInput::make('property_code')
                                    ->label('Property Code')
                                    ->disabled()
                                    ->dehydrated(false),
                                Select::make('property_type')
                                    ->label('Property Type')
                                    ->options([
                                        'land' => 'Land', 'house' => 'House', 'apartment' => 'Apartment',
                                        'commercial_building' => 'Commercial Building', 'office_space' => 'Office Space',
                                        'industrial_property' => 'Industrial Property', 'agricultural_land' => 'Agricultural Land',
                                        'other' => 'Other',
                                    ])
                                    ->native(false),
                                TextInput::make('area')
                                    ->label('Land Area')
                                    ->helperText('Unit varies: ropani / aana / sqft'),
                                TextInput::make('covered_area')->label('Covered Area'),
                                TextInput::make('kitta_no')->label('Kitta No.'),
                                TextInput::make('map_sheet_no')->label('Map Sheet No.'),
                            ]),

                        Section::make('Ownership & Legal')
                            ->icon('heroicon-o-identification')
                            ->columns(2)
                            ->schema([
                                Select::make('ownership_role')
                                    ->label('Ownership Role')
                                    ->options([
                                        'self' => 'Self', 'family_member' => 'Family Member',
                                        'authorized_representative' => 'Authorized Representative', 'company' => 'Company',
                                    ])
                                    ->native(false),
                                Select::make('ownership_type')
                                    ->label('Ownership Type')
                                    ->options(['private' => 'Private', 'joint' => 'Joint', 'other' => 'Other'])
                                    ->native(false),
                                TextInput::make('ownership_certificate_no')
                                    ->label('Ownership Certificate No.')
                                    ->helperText('Lalpurja number'),
                                TextInput::make('building_permit_no')
                                    ->label('Building Permit No.')
                                    ->helperText('Naksa Pass number'),
                            ]),

                        Section::make('Construction & Structure')
                            ->icon('heroicon-o-building-office-2')
                            ->columns(2)
                            ->collapsible()
                            ->schema([
                                TextInput::make('year_of_construction')->label('Year Built')->numeric(),
                                TextInput::make('no_of_floors')->label('No. of Floors')->numeric(),
                                TextInput::make('structure_type')
                                    ->label('Structure Type')
                                    ->helperText('RCC / Load Bearing / Steel / Other'),
                                TextInput::make('roof_type')->label('Roof Type'),
                                TextInput::make('facing_direction')->label('Facing Direction'),
                                Select::make('current_building_condition')
                                    ->label('Building Condition')
                                    ->options([
                                        'excellent' => 'Excellent', 'good' => 'Good',
                                        'fair' => 'Fair', 'poor' => 'Poor',
                                    ])
                                    ->native(false),
                            ]),

                        Section::make('Access & Utilities')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->columns(2)
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                TextInput::make('road_access')->label('Road Access'),
                                TextInput::make('road_width')->label('Road Width'),
                                TextInput::make('parking')->label('Parking'),
                                TextInput::make('water_supply')->label('Water Supply'),
                                TextInput::make('electricity')->label('Electricity'),
                                TextInput::make('internet')->label('Internet'),
                                TextInput::make('drainage')->label('Drainage'),
                            ]),
                    ]),

                // ---- Read-only context sidebar ----------------------------
                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 1])
                    ->schema([
                        Section::make('Owner')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                Placeholder::make('owner_name')
                                    ->label('Owner')
                                    ->content(fn (?Property $record) => $record?->owner
                                        ? trim("{$record->owner->full_name} ({$record->owner->client_code})", ' ()')
                                        : '—'),
                                Placeholder::make('owner_contact')
                                    ->label('Contact')
                                    ->content(fn (?Property $record) => $record?->owner
                                        ? collect([$record->owner->mobile_no, $record->owner->email])->filter()->implode(' · ') ?: '—'
                                        : '—'),
                                Placeholder::make('managed_by')
                                    ->label('Managed By (User)')
                                    ->content(fn (?Property $record) => $record?->user?->name ?? '—'),
                            ]),

                        Section::make('Location')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Placeholder::make('location_line')
                                    ->label('Address')
                                    ->content(fn (?Property $record) => $record?->address
                                        ? collect([
                                            $record->address->tole_locality,
                                            $record->address->municipality
                                                ? $record->address->municipality.' - '.$record->address->ward_no
                                                : null,
                                            $record->address->district,
                                            $record->address->province,
                                        ])->filter()->implode(', ') ?: '—'
                                        : '—'),
                                Placeholder::make('full_address_text')
                                    ->label('Full Address')
                                    ->content(fn (?Property $record) => $record?->address?->full_address_text ?? '—'),
                            ]),

                        Section::make('Record')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Placeholder::make('property_code_meta')
                                    ->label('Property Code')
                                    ->content(fn (?Property $record) => $record?->property_code ?? '—'),
                                Placeholder::make('created_at')
                                    ->label('Created')
                                    ->content(fn (?Property $record) => $record?->created_at?->format('d M Y, H:i') ?? '—'),
                                Placeholder::make('updated_at')
                                    ->label('Last Updated')
                                    ->content(fn (?Property $record) => $record?->updated_at?->format('d M Y, H:i') ?? '—'),
                            ]),
                    ]),

                // ---- Media, full width ------------------------------------
                Section::make('Property Photographs & Media')
                    ->description('Attached property photographs')
                    ->icon('heroicon-o-photo')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        FileUpload::make('property_photos')
                            ->label('Photographs')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->disk('public')
                            ->directory('properties/photos')
                            ->openable()
                            ->downloadable()
                            ->panelLayout('grid')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function approveAction(): Actions\Action
    {
        return Actions\Action::make('approve')
            ->label('Approve')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->hidden(fn (Property $record) => $record->approval_status === 'approved')
            ->action(function (Property $record) {
                $record->update([
                    'approval_status' => 'approved',
                    'status' => 'listed',
                    'is_listed' => true,
                ]);
                Notification::make()->title('Property approved & listed')->success()->send();
            });
    }

    public static function rejectAction(): Actions\Action
    {
        return Actions\Action::make('reject')
            ->label('Reject')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->hidden(fn (Property $record) => $record->approval_status === 'rejected')
            ->action(function (Property $record) {
                $record->update([
                    'approval_status' => 'rejected',
                    'status' => 'rejected',
                    'is_listed' => false,
                ]);
                Notification::make()->title('Property rejected')->danger()->send();
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photos.file_ref')
                    ->label('Photo')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(3),
                Tables\Columns\TextColumn::make('property_code')
                    ->label('Code')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner (User)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('property_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('area')
                    ->label('Area')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('address.municipality')
                    ->label('Municipality')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('approval_status')
                    ->label('Approval')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending'  => 'warning',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'listed' => 'success', 'draft' => 'gray',
                        'sold', 'rented', 'leased' => 'info',
                        'rejected', 'withdrawn' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\ToggleColumn::make('is_listed')
                    ->label('On Site')
                    ->onColor('success')
                    ->offColor('danger')
                    ->afterStateUpdated(function (Property $record, bool $state) {
                        Notification::make()
                            ->title($state ? 'Property is now visible on the marketplace' : 'Property hidden from the marketplace')
                            ->success()
                            ->send();
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Listed')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval_status')
                    ->label('Approval')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
                Tables\Filters\SelectFilter::make('property_type')
                    ->label('Type')
                    ->options([
                        'land' => 'Land', 'house' => 'House', 'apartment' => 'Apartment',
                        'commercial_building' => 'Commercial Building', 'office_space' => 'Office Space',
                    ]),
            ])
            ->actions([
                static::approveAction(),
                static::rejectAction(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit'   => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
