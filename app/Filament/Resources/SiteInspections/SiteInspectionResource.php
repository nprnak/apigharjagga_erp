<?php

namespace App\Filament\Resources\SiteInspections;

use App\Filament\Resources\SiteInspections\Pages\CreateSiteInspection;
use App\Filament\Resources\SiteInspections\Pages\EditSiteInspection;
use App\Filament\Resources\SiteInspections\Pages\ListSiteInspections;
use App\Filament\Resources\SiteInspections\Pages\ViewSiteInspection;
use App\Filament\Resources\SiteInspections\Schemas\SiteInspectionForm;
use App\Filament\Resources\SiteInspections\Schemas\SiteInspectionInfolist;
use App\Filament\Resources\SiteInspections\Tables\SiteInspectionsTable;
use App\Models\SiteInspection;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SiteInspectionResource extends Resource
{
    protected static ?string $model = SiteInspection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'inspection_id';

    public static function getNavigationGroup(): ?string
    {
        return 'Site Inspections';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', SiteInspection::STATUS_SUBMITTED)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    /**
     * Row-level scoping so engineers only see their own inspections and
     * valuation officers only see reports that have actually been
     * submitted for review. Admins / super admins see everything.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['property', 'inspector', 'reviewer']);
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('admin', 'admin') || $user->hasRole('super_admin', 'admin')) {
            return $query;
        }

        if ($user->hasRole('site_inspection_engineer', 'admin')) {
            return $query->where('inspector_user_id', $user->id);
        }

        if ($user->hasRole('valuation_officer', 'admin')) {
            return $query->where('status', '!=', SiteInspection::STATUS_DRAFT);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return SiteInspectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SiteInspectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiteInspectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteInspections::route('/'),
            'create' => CreateSiteInspection::route('/create'),
            'view' => ViewSiteInspection::route('/{record}'),
            'edit' => EditSiteInspection::route('/{record}/edit'),
        ];
    }

    /**
     * Notify every user in the target role via Filament's database
     * notifications (bell icon) that a report is waiting for their review.
     */
    public static function notifyRecipients(string $target, SiteInspection $record): void
    {
        $recipients = match ($target) {
            'valuation_officer' => User::role('valuation_officer', 'admin')->get(),
            'admin' => User::role('admin', 'admin')->get()
                ->merge(User::role('super_admin', 'admin')->get()),
            default => collect(),
        };

        $propertyCode = $record->property?->property_code ?? "#{$record->inspection_id}";

        foreach ($recipients->unique('id') as $recipient) {
            Notification::make()
                ->title('New site inspection to review')
                ->body("Property {$propertyCode} — inspection submitted for your review.")
                ->icon('heroicon-o-clipboard-document-check')
                ->actions([
                    NotificationAction::make('view')
                        ->url(static::getUrl('edit', ['record' => $record]))
                        ->markAsRead(),
                ])
                ->sendToDatabase($recipient);
        }
    }
}
