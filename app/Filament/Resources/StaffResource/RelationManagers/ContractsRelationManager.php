<?php

namespace App\Filament\Resources\StaffResource\RelationManagers;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'contracts';

    protected static ?string $title = 'Contracts';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('staff.manage');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('contract_type')
                ->options([
                    'permanent' => 'Permanent', 'fixed_term' => 'Fixed Term',
                    'probation' => 'Probation', 'part_time' => 'Part Time',
                ])
                ->required()
                ->native(false),
            DatePicker::make('start_date')->required(),
            DatePicker::make('end_date'),
            TextInput::make('salary')
                ->numeric()
                ->prefix('Rs.'),
            Select::make('status')
                ->options(['active' => 'Active', 'expired' => 'Expired', 'terminated' => 'Terminated'])
                ->default('active')
                ->required()
                ->native(false),
            FileUpload::make('file_path')
                ->disk('public')
                ->directory('staff/contracts')
                ->openable()
                ->downloadable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contract_type')
            ->columns([
                Tables\Columns\TextColumn::make('contract_type')->badge(),
                Tables\Columns\TextColumn::make('start_date')->date('d M Y'),
                Tables\Columns\TextColumn::make('end_date')->date('d M Y')->placeholder('—'),
                Tables\Columns\TextColumn::make('salary')->money('NPR')->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state) => match ($state) {
                    'active' => 'success', 'expired' => 'gray', 'terminated' => 'danger', default => 'gray',
                }),
            ])
            ->headerActions([
                Actions\CreateAction::make()->visible(fn () => static::canManage()),
            ])
            ->recordActions([
                Actions\EditAction::make()->visible(fn () => static::canManage()),
                Actions\DeleteAction::make()->visible(fn () => static::canManage()),
            ]);
    }
}
