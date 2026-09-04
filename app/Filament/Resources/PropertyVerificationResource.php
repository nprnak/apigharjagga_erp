<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\PropertyVerificationResource\Pages;
use App\Models\Property;
use App\Models\PropertyVerification;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PropertyVerificationResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = PropertyVerification::class;

    protected static ?string $navigationLabel = 'Property Verifications';

    protected static function permissionKey(): string
    {
        return 'verifications';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-clipboard-document-check';
    }

    public static function getNavigationGroup(): string|null
    {
        return 'Verification & Inspection';
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['property', 'verifier', 'approver']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Verification')
                ->columns(2)
                ->schema([
                    Select::make('property_id')
                        ->label('Property')
                        ->options(fn () => Property::pluck('property_code', 'property_id'))
                        ->required()
                        ->searchable(),
                    Select::make('verifier_staff_id')
                        ->label('Verifier')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->searchable(),
                    DatePicker::make('verification_date'),
                    Select::make('result')
                        ->options([
                            'verified' => 'Verified',
                            'verified_with_remarks' => 'Verified with Remarks',
                            'additional_documents_required' => 'Additional Documents Required',
                            'not_verified' => 'Not Verified',
                        ]),
                    Select::make('approver_staff_id')
                        ->label('Approver')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->searchable(),
                    DatePicker::make('approved_date'),
                    Toggle::make('gps_coordinates_recorded'),
                    Toggle::make('mis_entry_completed'),
                    Toggle::make('mobile_app_verification_completed'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.property_code')
                    ->label('Property'),
                Tables\Columns\TextColumn::make('verifier.full_name')
                    ->label('Verifier')
                    ->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('result')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'verified' => 'success',
                        'verified_with_remarks' => 'warning',
                        'not_verified' => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('Pending'),
                Tables\Columns\TextColumn::make('verification_date')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('approved_date')
                    ->date('d M Y')
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('result')
                    ->options([
                        'verified' => 'Verified',
                        'verified_with_remarks' => 'Verified with Remarks',
                        'additional_documents_required' => 'Additional Documents Required',
                        'not_verified' => 'Not Verified',
                    ]),
            ])
            ->actions([
                Actions\Action::make('markVerified')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PropertyVerification $record) => static::userHasPermission('verifications.manage') && $record->result !== 'verified')
                    ->requiresConfirmation()
                    ->action(function (PropertyVerification $record): void {
                        $record->update(['result' => 'verified', 'approved_date' => now()]);
                        Notification::make()->title('Property verified')->success()->send();
                    }),
                Actions\EditAction::make(),
            ])
            ->defaultSort('verification_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPropertyVerifications::route('/'),
            'create' => Pages\CreatePropertyVerification::route('/create'),
            'edit' => Pages\EditPropertyVerification::route('/{record}/edit'),
        ];
    }
}
