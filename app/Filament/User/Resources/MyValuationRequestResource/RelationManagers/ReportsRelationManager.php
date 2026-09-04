<?php

namespace App\Filament\User\Resources\MyValuationRequestResource\RelationManagers;

use Filament\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'reports';

    protected static ?string $title = 'Issued Valuation Reports';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('report_no')
                    ->label('Report No'),
                Tables\Columns\TextColumn::make('valuation_type')
                    ->label('Valuation Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('valuated_amount')
                    ->label('Valuated Amount')
                    ->money('NPR'),
                Tables\Columns\TextColumn::make('approval_status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('issued_date')
                    ->date('d M Y')
                    ->placeholder('Not issued'),
            ])
            ->headerActions([])
            ->actions([
                Actions\Action::make('download')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn ($record): bool => filled($record->report_file_ref))
                    ->action(fn ($record) => response()->download(
                        Storage::disk('public')->path($record->report_file_ref),
                        $record->report_no.'.pdf'
                    )),
            ])
            ->modifyQueryUsing(fn ($query) => $query->where('approval_status', 'approved'));
    }
}
