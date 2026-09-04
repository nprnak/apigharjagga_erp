<?php

namespace App\Filament\Resources\KycVerificationResource\Pages;

use App\Filament\Resources\KycVerificationResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewKycVerification extends ViewRecord
{
    protected static string $resource = KycVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve KYC')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => KycVerificationResource::canEdit($this->record) && $this->record->status !== 'approved')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'approved',
                        'admin_note' => null,
                        'reviewed_at' => now(),
                    ]);

                    Notification::make()
                        ->title('KYC approved')
                        ->success()
                        ->send();
                }),
            Action::make('reject')
                ->label('Reject KYC')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => KycVerificationResource::canEdit($this->record) && $this->record->status !== 'rejected')
                ->schema([
                    Textarea::make('admin_note')
                        ->label('Rejection reason (shown to user)')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'status' => 'rejected',
                        'admin_note' => $data['admin_note'],
                        'reviewed_at' => now(),
                    ]);

                    Notification::make()
                        ->title('KYC rejected')
                        ->danger()
                        ->send();
                }),
            Action::make('printPdf')
                ->label('Print PDF')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn (): string => route('admin.kyc.pdf', ['id' => $this->record->id]))
                ->openUrlInNewTab(),
        ];
    }
}
