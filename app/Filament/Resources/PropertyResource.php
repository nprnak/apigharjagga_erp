<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
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
        return $schema->components([
            Section::make('Property Details')
                ->columns(2)
                ->schema([
                    TextInput::make('property_code')->label('Property Code')->disabled(),
                    Select::make('property_type')
                        ->options([
                            'land' => 'Land', 'house' => 'House', 'apartment' => 'Apartment',
                            'commercial_building' => 'Commercial Building', 'office_space' => 'Office Space',
                            'industrial_property' => 'Industrial Property', 'agricultural_land' => 'Agricultural Land',
                            'other' => 'Other',
                        ]),
                    Select::make('approval_status')
                        ->label('Approval Status')
                        ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                        ->required(),
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft', 'listed' => 'Listed', 'under_verification' => 'Under Verification',
                            'under_valuation' => 'Under Valuation', 'under_negotiation' => 'Under Negotiation',
                            'sold' => 'Sold', 'rented' => 'Rented', 'leased' => 'Leased',
                            'withdrawn' => 'Withdrawn', 'rejected' => 'Rejected',
                        ]),
                    TextInput::make('kitta_no')->label('Kitta No.'),
                    TextInput::make('area')->label('Land Area'),
                    TextInput::make('covered_area')->label('Covered Area'),
                    TextInput::make('no_of_floors')->label('No. of Floors')->numeric(),
                    TextInput::make('year_of_construction')->label('Year Built')->numeric(),
                    TextInput::make('facing_direction')->label('Facing Direction'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (Property $record) => $record->approval_status === 'approved')
                    ->action(function (Property $record) {
                        $record->update(['approval_status' => 'approved', 'status' => 'listed']);
                        Notification::make()->title('Property approved & listed')->success()->send();
                    }),
                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->hidden(fn (Property $record) => $record->approval_status === 'rejected')
                    ->action(function (Property $record) {
                        $record->update(['approval_status' => 'rejected', 'status' => 'rejected']);
                        Notification::make()->title('Property rejected')->danger()->send();
                    }),
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
