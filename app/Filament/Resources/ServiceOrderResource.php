<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\ServiceOrderResource\Pages;
use App\Models\Client;
use App\Models\Property;
use App\Models\ServiceOrder;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceOrderResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = ServiceOrder::class;

    protected static ?string $navigationLabel = 'Service Orders';

    protected static ?int $navigationSort = 1;

    protected static function permissionKey(): string
    {
        return 'service_orders';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function getNavigationGroup(): string|null
    {
        return 'Service Delivery';
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['client', 'property']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Service Order')
                ->columns(2)
                ->schema([
                    TextInput::make('order_no')
                        ->required()
                        ->maxLength(30)
                        ->default(fn () => 'SO-'.Str::upper(Str::random(8))),
                    Select::make('status')
                        ->options([
                            'open' => 'Open',
                            'in_progress' => 'In Progress',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required(),
                    Select::make('client_id')
                        ->label('Client')
                        ->options(fn () => Client::pluck('full_name', 'client_id'))
                        ->required()
                        ->searchable(),
                    Select::make('property_id')
                        ->label('Property')
                        ->options(fn () => Property::pluck('property_code', 'property_id'))
                        ->required()
                        ->searchable(),
                    DatePicker::make('order_date'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_no')
                    ->label('Order No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client'),
                Tables\Columns\TextColumn::make('property.property_code')
                    ->label('Property'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'in_progress' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('order_date')
                    ->date('d M Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->defaultSort('order_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceOrders::route('/'),
            'create' => Pages\CreateServiceOrder::route('/create'),
            'edit' => Pages\EditServiceOrder::route('/{record}/edit'),
        ];
    }
}
