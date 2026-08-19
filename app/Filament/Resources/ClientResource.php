<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getNavigationGroup(): string|null
    {
        return 'Clients';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-identification';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Client Identity')
                ->columns(2)
                ->schema([
                    TextInput::make('client_code')->label('Client Code')->disabled(),
                    Select::make('client_type')
                        ->options([
                            'owner'    => 'Owner',
                            'buyer'    => 'Buyer',
                            'investor' => 'Investor',
                            'tenant'   => 'Tenant',
                            'agent'    => 'Agent',
                            'other'    => 'Other',
                        ])
                        ->required(),
                    TextInput::make('full_name')->label('Full Name')->required()->maxLength(150),
                    TextInput::make('father_mother_name')->label("Father / Mother Name")->maxLength(150),
                    TextInput::make('spouse_name')->label('Spouse Name')->maxLength(150),
                    TextInput::make('citizenship_no')->label('Citizenship No.')->maxLength(50),
                    DatePicker::make('date_of_birth')->label('Date of Birth'),
                    Select::make('gender')
                        ->options(['male' => 'Male', 'female' => 'Female', 'other' => 'Other']),
                    TextInput::make('nationality')->default('Nepali')->maxLength(50),
                    TextInput::make('occupation')->maxLength(100),
                    TextInput::make('mobile_no')->tel()->required()->maxLength(20),
                    TextInput::make('email')->email()->maxLength(150),
                ]),

            Section::make('Status')
                ->columns(2)
                ->schema([
                    Select::make('mis_entry_status')
                        ->label('MIS Entry Status')
                        ->options(['pending' => 'Pending', 'completed' => 'Completed']),
                    Toggle::make('is_active')->label('Active'),
                    DatePicker::make('registration_date')->label('Registration Date'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_code')
                    ->label('Client Code')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'owner'    => 'success',
                        'buyer'    => 'info',
                        'investor' => 'warning',
                        'tenant'   => 'gray',
                        'agent'    => 'primary',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('mobile_no')
                    ->label('Mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('registration_date')
                    ->label('Registered')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('client_type')
                    ->label('Type')
                    ->options([
                        'owner' => 'Owner', 'buyer' => 'Buyer',
                        'investor' => 'Investor', 'tenant' => 'Tenant', 'agent' => 'Agent',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('registration_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view'   => Pages\ViewClient::route('/{record}'),
            'edit'   => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
