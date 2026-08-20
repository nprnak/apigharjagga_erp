<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages;
use App\Models\PropertyInquiry;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class InquiryResource extends Resource
{
    protected static ?string $model = PropertyInquiry::class;

    protected static ?string $modelLabel = 'Inquiry';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function getNavigationGroup(): string|null
    {
        return 'Inquiries & Leads';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['property.address', 'listing']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Buyer / Tenant Details')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->label('Full Name')->disabled(),
                    TextInput::make('phone')->label('Phone')->disabled(),
                    TextInput::make('email')->label('Email')->disabled(),
                    TextInput::make('property.property_code')
                        ->label('Property Code')
                        ->disabled()
                        ->dehydrated(false),
                ]),

            Section::make('Property Inquired About')
                ->columns(2)
                ->schema([
                    TextEntry::make('property_name')
                        ->label('Property')
                        ->state(fn (PropertyInquiry $record) => static::propertyName($record)),
                    TextEntry::make('property_type')
                        ->label('Type')
                        ->state(fn (PropertyInquiry $record) => $record->property?->property_type
                            ? ucwords(str_replace('_', ' ', $record->property->property_type))
                            : '—'),
                    TextEntry::make('property_address')
                        ->label('Property Address')
                        ->state(fn (PropertyInquiry $record) => static::propertyAddress($record))
                        ->columnSpanFull(),
                ]),

            Section::make('Message')
                ->schema([
                    Textarea::make('message')
                        ->label('Inquiry Message')
                        ->rows(4)
                        ->disabled()
                        ->columnSpanFull(),
                ]),

            Section::make('Follow-up')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options([
                            'new' => 'New',
                            'contacted' => 'Contacted',
                            'closed' => 'Closed',
                        ])
                        ->required(),
                    Textarea::make('admin_note')
                        ->label('Internal Note')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Buyer / Tenant Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Full Name'),
                    TextEntry::make('phone')->label('Phone')->copyable(),
                    TextEntry::make('email')->label('Email')->placeholder('Not provided'),
                    TextEntry::make('created_at')->label('Received')->dateTime('d M Y, H:i'),
                ]),

            Section::make('Property Inquired About')
                ->columns(2)
                ->schema([
                    TextEntry::make('property_name')
                        ->label('Property')
                        ->state(fn (PropertyInquiry $record) => static::propertyName($record))
                        ->weight('bold'),
                    TextEntry::make('property.property_code')
                        ->label('Property Code')
                        ->badge()
                        ->color('info')
                        ->placeholder('Deleted property'),
                    TextEntry::make('property_address')
                        ->label('Property Address')
                        ->state(fn (PropertyInquiry $record) => static::propertyAddress($record))
                        ->columnSpanFull(),
                    TextEntry::make('property.property_type')
                        ->label('Type')
                        ->formatStateUsing(fn ($state) => $state ? ucwords(str_replace('_', ' ', $state)) : '—'),
                    TextEntry::make('property.area')
                        ->label('Area')
                        ->placeholder('—'),
                    TextEntry::make('property.covered_area')
                        ->label('Covered Area')
                        ->placeholder('—'),
                    TextEntry::make('listing.purpose_of_listing')
                        ->label('Listing Purpose')
                        ->badge()
                        ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : '—'),
                ]),

            Section::make('Inquiry Message')
                ->schema([
                    TextEntry::make('message')
                        ->label('')
                        ->placeholder('No message was left.')
                        ->columnSpanFull(),
                ]),

            Section::make('Follow-up')
                ->columns(2)
                ->schema([
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'new' => 'warning',
                            'contacted' => 'info',
                            'closed' => 'success',
                            default => 'gray',
                        }),
                    TextEntry::make('admin_note')
                        ->label('Internal Note')
                        ->placeholder('No internal notes yet.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    private static function propertyName(PropertyInquiry $record): string
    {
        $property = $record->property;

        if (! $property) {
            return 'Property no longer available';
        }

        $type = $property->property_type
            ? ucwords(str_replace('_', ' ', $property->property_type))
            : 'Property';

        $municipality = $property->address?->municipality;

        return $municipality ? "{$type} in {$municipality}" : $type;
    }

    private static function propertyAddress(PropertyInquiry $record): string
    {
        $address = $record->property?->address;

        if (! $address) {
            return 'Address not available';
        }

        $parts = array_filter([
            $address->tole_locality,
            $address->ward_no ? "Ward {$address->ward_no}" : null,
            $address->municipality,
            $address->district,
            $address->province,
        ]);

        return $parts ? implode(', ', $parts) : 'Address not available';
    }

    public static function markContactedAction(): Actions\Action
    {
        return Actions\Action::make('mark_contacted')
            ->label('Mark Contacted')
            ->icon('heroicon-o-phone')
            ->color('info')
            ->hidden(fn (PropertyInquiry $record) => $record->status !== 'new')
            ->action(function (PropertyInquiry $record) {
                $record->update(['status' => 'contacted']);
                Notification::make()->title('Inquiry marked as contacted')->success()->send();
            });
    }

    public static function markClosedAction(): Actions\Action
    {
        return Actions\Action::make('mark_closed')
            ->label('Close')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->hidden(fn (PropertyInquiry $record) => $record->status === 'closed')
            ->action(function (PropertyInquiry $record) {
                $record->update(['status' => 'closed']);
                Notification::make()->title('Inquiry closed')->success()->send();
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('property.property_code')
                    ->label('Property')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->placeholder('Deleted property'),
                Tables\Columns\TextColumn::make('property.property_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => $state ? ucwords(str_replace('_', ' ', $state)) : '—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('listing.purpose_of_listing')
                    ->label('Purpose')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : '—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->limit(40)
                    ->tooltip(fn (PropertyInquiry $record) => $record->message)
                    ->wrap(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'contacted' => 'info',
                        'closed' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'closed' => 'Closed',
                    ]),
            ])
            ->actions([
                static::markContactedAction(),
                static::markClosedAction(),
                Actions\ViewAction::make(),
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
            'index' => Pages\ListInquiries::route('/'),
            'view' => Pages\ViewInquiry::route('/{record}'),
            'edit' => Pages\EditInquiry::route('/{record}/edit'),
        ];
    }
}
