<?php

namespace App\Filament\Resources\PayrollRunResource\RelationManagers;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PayslipsRelationManager extends RelationManager
{
    protected static string $relationship = 'payslips';

    protected static ?string $title = 'Payslips';

    protected static function canEditPayslips(Model $payrollRun): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('payroll.manage') && $payrollRun->status === 'draft';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('basic_salary')->numeric()->prefix('Rs.')->disabled(),
            TextInput::make('allowances')
                ->numeric()
                ->prefix('Rs.')
                ->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Get $get, Set $set) => $set('net_pay', (float) $get('basic_salary') + (float) $get('allowances') - (float) $get('deductions'))),
            TextInput::make('deductions')
                ->numeric()
                ->prefix('Rs.')
                ->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Get $get, Set $set) => $set('net_pay', (float) $get('basic_salary') + (float) $get('allowances') - (float) $get('deductions'))),
            TextInput::make('net_pay')->numeric()->prefix('Rs.')->disabled()->dehydrated(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('staff_id')
            ->columns([
                Tables\Columns\TextColumn::make('staff.full_name')->label('Staff'),
                Tables\Columns\TextColumn::make('basic_salary')->money('NPR'),
                Tables\Columns\TextColumn::make('allowances')->money('NPR'),
                Tables\Columns\TextColumn::make('deductions')->money('NPR'),
                Tables\Columns\TextColumn::make('net_pay')->money('NPR'),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->visible(fn () => static::canEditPayslips($this->getOwnerRecord())),
            ]);
    }
}
