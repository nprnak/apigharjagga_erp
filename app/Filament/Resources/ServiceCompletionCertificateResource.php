<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\ServiceCompletionCertificateResource\Pages;
use App\Models\Client;
use App\Models\Property;
use App\Models\ServiceCompletionCertificate;
use App\Models\ServiceOrder;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceCompletionCertificateResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = ServiceCompletionCertificate::class;

    protected static ?string $navigationLabel = 'Completion Certificates';

    protected static ?int $navigationSort = 2;

    protected static function permissionKey(): string
    {
        return 'completions';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-check';
    }

    public static function getNavigationGroup(): string|null
    {
        return 'Service Delivery';
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['client', 'property', 'serviceOrder']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Certificate')
                ->columns(2)
                ->schema([
                    TextInput::make('certificate_no')
                        ->required()
                        ->maxLength(30)
                        ->default(fn () => 'SCC-'.Str::upper(Str::random(8))),
                    DatePicker::make('issue_date'),
                    Select::make('client_id')
                        ->label('Client')
                        ->options(fn () => Client::pluck('full_name', 'client_id'))
                        ->required()
                        ->searchable(),
                    Select::make('property_id')
                        ->label('Property')
                        ->options(fn () => Property::pluck('property_code', 'property_id'))
                        ->required()
                        ->searchable(),
                    Select::make('service_order_id')
                        ->label('Service Order')
                        ->options(fn () => ServiceOrder::pluck('order_no', 'order_id'))
                        ->searchable(),
                    Select::make('final_status')
                        ->options(['completed' => 'Completed', 'completed_with_remarks' => 'Completed with Remarks'])
                        ->required(),
                    DatePicker::make('service_start_date'),
                    DatePicker::make('service_completion_date'),
                    Select::make('assigned_officer_staff_id')
                        ->label('Assigned Officer')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->searchable(),
                    Select::make('technical_reviewer_staff_id')
                        ->label('Technical Reviewer')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->searchable(),
                    DatePicker::make('client_acceptance_date'),
                    Textarea::make('client_remarks')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('certificate_no')
                    ->label('Certificate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client'),
                Tables\Columns\TextColumn::make('property.property_code')
                    ->label('Property'),
                Tables\Columns\TextColumn::make('final_status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'completed' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('client_acceptance_date')
                    ->date('d M Y')
                    ->placeholder('Not accepted'),
                Tables\Columns\TextColumn::make('issue_date')
                    ->date('d M Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('final_status')
                    ->options(['completed' => 'Completed', 'completed_with_remarks' => 'Completed with Remarks']),
            ])
            ->actions([
                Actions\Action::make('recordAcceptance')
                    ->label('Record Client Acceptance')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ServiceCompletionCertificate $record) => static::userHasPermission('completions.manage') && ! $record->client_acceptance_date)
                    ->requiresConfirmation()
                    ->action(function (ServiceCompletionCertificate $record): void {
                        $record->update(['client_acceptance_date' => now()]);

                        if ($record->serviceOrder) {
                            $record->serviceOrder->update(['status' => 'completed']);
                        }

                        Notification::make()->title('Client acceptance recorded')->success()->send();
                    }),
                Actions\EditAction::make(),
            ])
            ->defaultSort('issue_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceCompletionCertificates::route('/'),
            'create' => Pages\CreateServiceCompletionCertificate::route('/create'),
            'edit' => Pages\EditServiceCompletionCertificate::route('/{record}/edit'),
        ];
    }
}
