<?php

namespace App\Filament\Resources\ValuationRequestResource\RelationManagers;

use App\Models\Staff;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'reports';

    protected static ?string $title = 'Valuation Reports';

    private static function calculateAmount(array $data): float
    {
        $landValue = (float) ($data['land_area'] ?? 0) * (float) ($data['land_rate'] ?? 0);
        $buildingValue = (float) ($data['building_area'] ?? 0) * (float) ($data['building_rate'] ?? 0);
        $depreciation = min(100, max(0, (float) ($data['depreciation_percent'] ?? 0)));

        return max(0, round($landValue + ($buildingValue * (1 - ($depreciation / 100))) + (float) ($data['adjustment_amount'] ?? 0), 2));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('report_no')
                ->required()
                ->maxLength(30)
                ->default(fn () => 'VR-'.Str::upper(Str::random(8))),
            Select::make('valuation_type')
                ->options([
                    'market_value' => 'Market Value',
                    'forced_sale_value' => 'Forced Sale Value',
                    'mortgage_valuation' => 'Mortgage Valuation',
                    'fair_value' => 'Fair Value',
                    'insurance_value' => 'Insurance Value',
                    'investment_value' => 'Investment Value',
                    'rental_value' => 'Rental Value',
                    'government_valuation' => 'Government Valuation',
                    'asset_valuation' => 'Asset Valuation',
                ])
                ->required(),
            TextInput::make('land_area')
                ->label('Land Area')
                ->numeric()
                ->minValue(0)
                ->required()
                ->live(onBlur: true),
            TextInput::make('land_rate')
                ->label('Land Rate / Unit (NPR)')
                ->numeric()
                ->minValue(0)
                ->required()
                ->live(onBlur: true),
            TextInput::make('building_area')
                ->label('Building Area')
                ->numeric()
                ->minValue(0)
                ->required()
                ->live(onBlur: true),
            TextInput::make('building_rate')
                ->label('Building Rate / Unit (NPR)')
                ->numeric()
                ->minValue(0)
                ->required()
                ->live(onBlur: true),
            TextInput::make('depreciation_percent')
                ->label('Building Depreciation (%)')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->required()
                ->live(onBlur: true),
            TextInput::make('adjustment_amount')
                ->label('Other Adjustment (NPR)')
                ->numeric()
                ->required()
                ->live(onBlur: true),
            Placeholder::make('calculated_total')
                ->label('Calculated Valuation')
                ->content(fn (Get $get): string => 'NPR '.number_format(self::calculateAmount([
                    'land_area' => $get('land_area'),
                    'land_rate' => $get('land_rate'),
                    'building_area' => $get('building_area'),
                    'building_rate' => $get('building_rate'),
                    'depreciation_percent' => $get('depreciation_percent'),
                    'adjustment_amount' => $get('adjustment_amount'),
                ]), 2)),
            TextInput::make('valuated_amount')
                ->label('Final Valuated Amount (NPR)')
                ->disabled()
                ->dehydrated(false),
            Select::make('valuator_staff_id')
                ->label('Valuator')
                ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                ->searchable(),
            TextInput::make('rate_basis')
                ->label('Rate Basis / Notes'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('report_no')
            ->columns([
                Tables\Columns\TextColumn::make('report_no')
                    ->label('Report No'),
                Tables\Columns\TextColumn::make('valuation_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('valuated_amount')
                    ->money('NPR'),
                Tables\Columns\TextColumn::make('valuator.full_name')
                    ->label('Valuator')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('approval_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending_approval' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['property_id'] = $this->getOwnerRecord()->property_id;
                        $data['valuator_staff_id'] ??= $this->getOwnerRecord()->assigned_valuator_staff_id;
                        $data['approval_status'] = 'draft';
                        $data['valuated_amount'] = self::calculateAmount($data);

                        return $data;
                    })
                    ->after(fn () => $this->getOwnerRecord()->update(['status' => 'in_progress'])),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => [...$data, 'valuated_amount' => self::calculateAmount($data)]),
                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Model $record) => $record->approval_status === 'pending_approval')
                    ->requiresConfirmation()
                    ->schema([
                        Select::make('approved_by_staff_id')
                            ->label('Approved By')
                            ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (Model $record, array $data): void {
                        $issuedDate = now();
                        $record->update([
                            'approval_status' => 'approved',
                            'issued_date' => $issuedDate,
                            'approved_by_staff_id' => $data['approved_by_staff_id'],
                            'digitally_signed' => true,
                        ]);
                        $record->refresh()->load(['request.client', 'property', 'valuator', 'approver']);
                        $filePath = 'valuation-reports/'.$record->report_no.'.pdf';
                        Storage::disk('public')->put($filePath, Pdf::loadView('pdf.valuation_report', ['report' => $record])->output());

                        $record->update([
                            'report_file_ref' => $filePath,
                        ]);

                        // Mark the parent request as report-issued once its report is approved.
                        $record->request()->update(['status' => 'report_issued']);

                        Notification::make()->title('Valuation report approved')->success()->send();
                    }),
                Actions\Action::make('submitForApproval')
                    ->label('Submit for Approval')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (Model $record) => $record->approval_status === 'draft')
                    ->requiresConfirmation()
                    ->action(function (Model $record): void {
                        $record->update(['approval_status' => 'pending_approval']);
                        Notification::make()->title('Report submitted for approval')->success()->send();
                    }),
                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Model $record) => $record->approval_status === 'pending_approval')
                    ->requiresConfirmation()
                    ->action(function (Model $record): void {
                        $record->update(['approval_status' => 'rejected']);
                        Notification::make()->title('Valuation report rejected')->warning()->send();
                    }),
                Actions\Action::make('download')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn (Model $record) => $record->approval_status === 'approved' && filled($record->report_file_ref))
                    ->action(fn (Model $record) => response()->download(
                        Storage::disk('public')->path($record->report_file_ref),
                        $record->report_no.'.pdf'
                    )),
                Actions\DeleteAction::make(),
            ]);
    }
}
