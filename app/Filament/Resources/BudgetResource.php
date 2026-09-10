<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\BudgetResource\Pages;
use App\Models\Budget;
use App\Models\FinanceAccount;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BudgetResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = Budget::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'budget_id';

    protected static function permissionKey(): string
    {
        return 'budgets';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-calculator';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('account');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Budget Allocation')
                ->columns(2)
                ->schema([
                    Select::make('account_id')
                        ->label('Account')
                        ->options(fn () => FinanceAccount::whereIn('account_type', ['income', 'expense'])->orderBy('account_code')->pluck('account_name', 'account_id'))
                        ->required()
                        ->searchable(),
                    TextInput::make('fiscal_year')
                        ->label('Fiscal Year')
                        ->helperText('Plain 4-digit year, e.g. 2026')
                        ->required()
                        ->maxLength(20),
                    Select::make('period_type')
                        ->options(['annual' => 'Annual', 'monthly' => 'Monthly'])
                        ->default('annual')
                        ->required()
                        ->live()
                        ->native(false),
                    TextInput::make('period_label')
                        ->label('Month')
                        ->helperText('Format: YYYY-MM, e.g. 2026-04')
                        ->visible(fn ($get) => $get('period_type') === 'monthly')
                        ->required(fn ($get) => $get('period_type') === 'monthly'),
                    TextInput::make('allocated_amount')
                        ->numeric()
                        ->prefix('Rs.')
                        ->required(),
                    Textarea::make('notes')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account.account_name')
                    ->label('Account')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fiscal_year'),
                Tables\Columns\TextColumn::make('period_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state, Budget $record) => $state === 'monthly' ? $record->period_label : 'Annual'),
                Tables\Columns\TextColumn::make('allocated_amount')
                    ->label('Allocated')
                    ->money('NPR'),
                Tables\Columns\TextColumn::make('actual')
                    ->label('Actual')
                    ->state(fn (Budget $record) => $record->actualAmount())
                    ->money('NPR'),
                Tables\Columns\TextColumn::make('variance')
                    ->label('Variance')
                    ->state(function (Budget $record) {
                        $variance = (float) $record->allocated_amount - $record->actualAmount();

                        return $record->account->account_type === 'expense' ? $variance : -$variance;
                    })
                    ->money('NPR')
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('fiscal_year')
                    ->options(fn () => Budget::query()->distinct()->pluck('fiscal_year', 'fiscal_year')),
            ])
            ->defaultSort('fiscal_year', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}
