<?php

namespace App\Filament\Resources\JobOpeningResource\RelationManagers;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    protected static ?string $title = 'Applicants';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('careers.manage');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')
                ->options([
                    'received' => 'Received', 'reviewed' => 'Reviewed',
                    'shortlisted' => 'Shortlisted', 'rejected' => 'Rejected', 'hired' => 'Hired',
                ])
                ->required()
                ->native(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('applicant_name')
            ->columns([
                Tables\Columns\TextColumn::make('applicant_name')->label('Applicant')->searchable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone')->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'hired' => 'success', 'shortlisted' => 'info',
                        'rejected' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('applied_at')->dateTime('d M Y'),
            ])
            ->recordActions([
                Actions\Action::make('viewResume')
                    ->label('Resume')
                    ->icon('heroicon-o-document')
                    ->visible(fn ($record) => filled($record->resume_path))
                    ->url(fn ($record) => Storage::disk('public')->url($record->resume_path))
                    ->openUrlInNewTab(),
                Actions\EditAction::make()->visible(fn () => static::canManage()),
                Actions\DeleteAction::make()->visible(fn () => static::canManage()),
            ])
            ->defaultSort('applied_at', 'desc');
    }
}
