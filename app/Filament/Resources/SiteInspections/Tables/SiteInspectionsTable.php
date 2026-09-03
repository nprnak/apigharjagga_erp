<?php

namespace App\Filament\Resources\SiteInspections\Tables;

use App\Filament\Resources\SiteInspections\SiteInspectionResource;
use App\Models\SiteInspection;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SiteInspectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.property_code')
                    ->label('Property')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('inspector.name')
                    ->label('Inspector')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('inspection_date')
                    ->label('Inspection Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'reviewed' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('submitted_to')
                    ->label('Submitted To')
                    ->formatStateUsing(fn (?string $state) => $state ? str($state)->headline() : '—')
                    ->badge()
                    ->color('info'),
                TextColumn::make('final_status')
                    ->label('Final Status')
                    ->formatStateUsing(fn (?string $state) => $state ? str($state)->headline() : '—')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'suitable_for_listing' => 'success',
                        'requires_additional_verification' => 'warning',
                        'not_recommended' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'reviewed' => 'Reviewed',
                    ]),
                SelectFilter::make('submitted_to')
                    ->label('Submitted To')
                    ->options([
                        'valuation_officer' => 'Valuation Officer',
                        'admin' => 'Admin',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('submitToValuationOfficer')
                    ->label('Send to Valuation Officer')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (SiteInspection $record) => $record->status === SiteInspection::STATUS_DRAFT
                        && auth()->user()?->hasRole('site_inspection_engineer', 'admin'))
                    ->action(fn (SiteInspection $record) => static::submit($record, 'valuation_officer')),
                Action::make('submitToAdmin')
                    ->label('Send to Admin')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (SiteInspection $record) => $record->status === SiteInspection::STATUS_DRAFT
                        && auth()->user()?->hasRole('site_inspection_engineer', 'admin'))
                    ->action(fn (SiteInspection $record) => static::submit($record, 'admin')),
                Action::make('markReviewed')
                    ->label('Mark Reviewed')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (SiteInspection $record) => $record->status === SiteInspection::STATUS_SUBMITTED
                        && (auth()->user()?->hasRole('valuation_officer', 'admin')
                            || auth()->user()?->hasRole('admin', 'admin')
                            || auth()->user()?->hasRole('super_admin', 'admin')))
                    ->action(function (SiteInspection $record) {
                        $record->update([
                            'status' => SiteInspection::STATUS_REVIEWED,
                            'reviewed_by_user_id' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);
                        Notification::make()->title('Inspection marked as reviewed')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function submit(SiteInspection $record, string $target): void
    {
        $record->update([
            'status' => SiteInspection::STATUS_SUBMITTED,
            'submitted_to' => $target,
            'submitted_at' => now(),
            'inspector_user_id' => $record->inspector_user_id ?? auth()->id(),
        ]);

        SiteInspectionResource::notifyRecipients($target, $record);

        Notification::make()
            ->title('Sent to '.str($target)->headline())
            ->success()
            ->send();
    }
}
