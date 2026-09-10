<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Contractor;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ContractorAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'contractorAssignments';

    protected static ?string $title = 'Contractors';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('projects.manage');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('contractor_id')
                ->label('Contractor')
                ->options(fn () => Contractor::where('is_active', true)->pluck('name', 'contractor_id'))
                ->required()
                ->searchable(),
            TextInput::make('work_scope')->maxLength(255)->columnSpanFull(),
            TextInput::make('contract_amount')->numeric()->prefix('Rs.'),
            DatePicker::make('start_date'),
            DatePicker::make('end_date'),
            Select::make('status')
                ->options(['active' => 'Active', 'completed' => 'Completed', 'terminated' => 'Terminated'])
                ->default('active')
                ->required()
                ->native(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('work_scope')
            ->columns([
                Tables\Columns\TextColumn::make('contractor.name')->label('Contractor'),
                Tables\Columns\TextColumn::make('work_scope')->placeholder('—'),
                Tables\Columns\TextColumn::make('contract_amount')->money('NPR')->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state) => match ($state) {
                    'active' => 'success', 'terminated' => 'danger', default => 'gray',
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
