<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyPowerOfAttorneyResource\Pages;
use App\Models\PowerOfAttorney;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Lets an Agent-type account request authorization to manage a property
 * owner's listings. The owner is identified by mobile number or
 * citizenship number (whichever staff/self-service registration captured)
 * rather than requiring the owner to have a web login themselves — see
 * User::resolvedClient() for why Client, not User, is the ownership
 * reference that's guaranteed to exist.
 */
class MyPowerOfAttorneyResource extends Resource
{
    protected static ?string $model = PowerOfAttorney::class;

    protected static ?string $navigationLabel = 'Power of Attorney';

    protected static ?string $modelLabel = 'Power of Attorney';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'poa_id';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-check';
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->client_type === 'agent';
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->client_type === 'agent';
    }

    public static function getEloquentQuery(): Builder
    {
        $userId = Auth::id();

        return parent::getEloquentQuery()
            ->where('agent_user_id', $userId)
            ->with('ownerClient');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request Authorization')
                ->description("Enter the property owner's mobile number or citizenship number exactly as registered with Api Ghar Jagga, and attach the signed Power of Attorney document. Our team will verify it before you gain access to that owner's properties.")
                ->columns(1)
                ->schema([
                    TextInput::make('owner_identifier')
                        ->label("Owner's Mobile Number or Citizenship Number")
                        ->required()
                        ->maxLength(50)
                        ->dehydrated(false),
                    FileUpload::make('document_path')
                        ->label('Power of Attorney Document')
                        ->required()
                        ->disk('public')
                        ->directory('poa')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(10240),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ownerClient.full_name')
                    ->label('Owner')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Staff Remarks')
                    ->placeholder('—')
                    ->limit(60),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyPowerOfAttorneys::route('/'),
            'create' => Pages\CreateMyPowerOfAttorney::route('/create'),
        ];
    }
}
