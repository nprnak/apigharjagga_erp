<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\FinanceAccountResource\Pages;
use App\Models\FinanceAccount;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FinanceAccountResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = FinanceAccount::class;

    protected static ?string $navigationLabel = 'Chart of Accounts';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'account_name';

    protected static function permissionKey(): string
    {
        return 'finance_accounts';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-book-open';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }

    /**
     * System accounts (Cash, the income categories the app posts to
     * automatically) can't be deleted — doing so would orphan existing
     * ledger postings.
     */
    public static function canDelete(Model $record): bool
    {
        return static::userHasPermission('finance_accounts.manage') && ! $record->is_system;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account')
                ->columns(2)
                ->schema([
                    TextInput::make('account_code')
                        ->required()
                        ->maxLength(20)
                        ->unique(ignoreRecord: true),
                    TextInput::make('account_name')
                        ->required()
                        ->maxLength(150),
                    Select::make('account_type')
                        ->options([
                            'asset' => 'Asset',
                            'liability' => 'Liability',
                            'equity' => 'Equity',
                            'income' => 'Income',
                            'expense' => 'Expense',
                        ])
                        ->required()
                        ->native(false),
                    Toggle::make('is_active')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account_code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('account_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'asset' => 'info',
                        'liability' => 'warning',
                        'equity' => 'gray',
                        'income' => 'success',
                        'expense' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Current Balance')
                    ->state(fn (FinanceAccount $record) => $record->balanceAsOf())
                    ->money('NPR'),
                Tables\Columns\IconColumn::make('is_system')
                    ->label('System')
                    ->boolean(),
                Tables\Columns\ToggleColumn::make('is_active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('account_type')
                    ->options([
                        'asset' => 'Asset', 'liability' => 'Liability', 'equity' => 'Equity',
                        'income' => 'Income', 'expense' => 'Expense',
                    ]),
            ])
            ->defaultSort('account_code');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinanceAccounts::route('/'),
            'create' => Pages\CreateFinanceAccount::route('/create'),
            'edit' => Pages\EditFinanceAccount::route('/{record}/edit'),
        ];
    }
}
