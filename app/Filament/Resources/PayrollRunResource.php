<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\PayrollRunResource\Pages;
use App\Filament\Resources\PayrollRunResource\RelationManagers\PayslipsRelationManager;
use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use App\Models\PayrollRun;
use App\Models\Payslip;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PayrollRunResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = PayrollRun::class;

    protected static ?string $navigationLabel = 'Payroll';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'period_month';

    protected static function permissionKey(): string
    {
        return 'payroll';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Human Resources';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withSum('payslips', 'net_pay');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payroll Run')
                ->columns(2)
                ->schema([
                    TextInput::make('period_month')
                        ->label('Period')
                        ->helperText('Format: YYYY-MM, e.g. 2026-04')
                        ->required()
                        ->maxLength(7)
                        ->disabled(fn (?PayrollRun $record) => $record !== null)
                        ->dehydrated(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('period_month')
                    ->label('Period'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'finalized' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('payslips_count')
                    ->label('Employees')
                    ->counts('payslips'),
                Tables\Columns\TextColumn::make('payslips_sum_net_pay')
                    ->label('Total Net Pay')
                    ->money('NPR'),
            ])
            ->actions([
                Actions\Action::make('generatePayslips')
                    ->label('Generate Payslips')
                    ->icon('heroicon-o-document-plus')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalDescription('Creates a payslip for every active staff member with a basic salary set, using their current basic_salary. Re-running updates the basic_salary on existing payslips without touching allowances/deductions you\'ve already adjusted.')
                    ->visible(fn (PayrollRun $record) => static::userHasPermission('payroll.manage') && $record->status === 'draft')
                    ->action(function (PayrollRun $record): void {
                        $staff = Staff::where('is_active', true)->whereNotNull('basic_salary')->get();

                        foreach ($staff as $member) {
                            $payslip = Payslip::firstOrNew([
                                'payroll_run_id' => $record->payroll_run_id,
                                'staff_id' => $member->staff_id,
                            ]);
                            $payslip->basic_salary = $member->basic_salary;
                            $payslip->allowances ??= 0;
                            $payslip->deductions ??= 0;
                            $payslip->net_pay = (float) $payslip->basic_salary + (float) $payslip->allowances - (float) $payslip->deductions;
                            $payslip->save();
                        }

                        Notification::make()->title("Generated payslips for {$staff->count()} staff")->success()->send();
                    }),
                Actions\Action::make('finalizeAndPost')
                    ->label('Finalize & Post to Ledger')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Posts one Salaries & Wages expense transaction for the total net pay of this run and locks it from further changes. This cannot be undone from here.')
                    ->visible(fn (PayrollRun $record) => static::userHasPermission('payroll.approve') && $record->status === 'draft')
                    ->action(function (PayrollRun $record): void {
                        $total = (float) $record->payslips()->sum('net_pay');

                        if ($total <= 0) {
                            Notification::make()->title('Generate payslips before finalizing')->danger()->send();

                            return;
                        }

                        $salaries = FinanceAccount::where('account_code', '5010')->first();
                        $cash = FinanceAccount::where('account_code', FinanceAccount::CODE_CASH)->first();

                        if (! $salaries || ! $cash) {
                            Notification::make()->title('Salaries or Cash account not found — cannot post')->danger()->send();

                            return;
                        }

                        FinanceTransaction::postBalanced(
                            [
                                'transaction_date' => now()->toDateString(),
                                'reference_type' => 'payroll_run',
                                'reference_id' => $record->payroll_run_id,
                                'description' => "Payroll {$record->period_month}",
                            ],
                            [
                                ['account_id' => $salaries->account_id, 'debit' => $total],
                                ['account_id' => $cash->account_id, 'credit' => $total],
                            ],
                        );

                        $record->update(['status' => 'finalized', 'finalized_at' => now()]);

                        Notification::make()->title('Payroll finalized and posted to the ledger')->success()->send();
                    }),
            ])
            ->defaultSort('period_month', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PayslipsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrollRuns::route('/'),
            'create' => Pages\CreatePayrollRun::route('/create'),
            'edit' => Pages\EditPayrollRun::route('/{record}/edit'),
        ];
    }
}
