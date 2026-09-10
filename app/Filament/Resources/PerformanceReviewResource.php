<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\PerformanceReviewResource\Pages;
use App\Models\PerformanceReview;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PerformanceReviewResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = PerformanceReview::class;

    protected static ?string $navigationLabel = 'Performance Reviews';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'review_id';

    protected static function permissionKey(): string
    {
        return 'performance';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-star';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Human Resources';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['staff', 'reviewer']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Review')
                ->columns(2)
                ->schema([
                    Select::make('staff_id')
                        ->label('Staff')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->required()
                        ->searchable(),
                    Select::make('reviewer_staff_id')
                        ->label('Reviewer')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->searchable(),
                    TextInput::make('review_period')
                        ->helperText('e.g. "2026 Q1" or "2026-04"')
                        ->required()
                        ->maxLength(20),
                    DatePicker::make('review_date')
                        ->default(now())
                        ->required(),
                    Select::make('rating')
                        ->options([1 => '1 — Needs Improvement', 2 => '2 — Below Expectations', 3 => '3 — Meets Expectations', 4 => '4 — Exceeds Expectations', 5 => '5 — Outstanding'])
                        ->required()
                        ->native(false),
                    Textarea::make('strengths')
                        ->columnSpanFull(),
                    Textarea::make('areas_for_improvement')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('staff.full_name')
                    ->label('Staff')
                    ->searchable(),
                Tables\Columns\TextColumn::make('review_period')
                    ->label('Period'),
                Tables\Columns\TextColumn::make('rating')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state === 3 => 'info',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('reviewer.full_name')
                    ->label('Reviewer')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('review_date')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->defaultSort('review_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerformanceReviews::route('/'),
            'create' => Pages\CreatePerformanceReview::route('/create'),
            'edit' => Pages\EditPerformanceReview::route('/{record}/edit'),
        ];
    }
}
