<?php

namespace App\Filament\Resources\SiteInspections\Pages;

use App\Filament\Resources\SiteInspections\SiteInspectionResource;
use App\Filament\Resources\SiteInspections\Tables\SiteInspectionsTable;
use App\Models\SiteInspection;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSiteInspection extends EditRecord
{
    protected static string $resource = SiteInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('submitToValuationOfficer')
                ->label('Send to Valuation Officer')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === SiteInspection::STATUS_DRAFT
                    && auth()->user()?->hasRole('site_inspection_engineer', 'admin'))
                ->action(function () {
                    SiteInspectionsTable::submit($this->record, 'valuation_officer');
                    $this->redirect(static::getResource()::getUrl('edit', ['record' => $this->record]));
                }),
            Action::make('submitToAdmin')
                ->label('Send to Admin')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === SiteInspection::STATUS_DRAFT
                    && auth()->user()?->hasRole('site_inspection_engineer', 'admin'))
                ->action(function () {
                    SiteInspectionsTable::submit($this->record, 'admin');
                    $this->redirect(static::getResource()::getUrl('edit', ['record' => $this->record]));
                }),
            Action::make('markReviewed')
                ->label('Mark Reviewed')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === SiteInspection::STATUS_SUBMITTED
                    && (auth()->user()?->hasRole('valuation_officer', 'admin')
                        || auth()->user()?->hasRole('admin', 'admin')
                        || auth()->user()?->hasRole('super_admin', 'admin')))
                ->action(function () {
                    $this->record->update([
                        'status' => SiteInspection::STATUS_REVIEWED,
                        'reviewed_by_user_id' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);
                    Notification::make()->title('Inspection marked as reviewed')->success()->send();
                    $this->redirect(static::getResource()::getUrl('edit', ['record' => $this->record]));
                }),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
