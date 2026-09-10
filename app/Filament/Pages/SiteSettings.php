<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class SiteSettings extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.site-settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 100;

    /**
     * @var array<string, mixed>
     */
    public array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::allSettings()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General')
                    ->description('Basic information shown across the public site.')
                    ->components([
                        TextInput::make('site_name')
                            ->label('Site Name')
                            ->maxLength(255),
                        TextInput::make('support_email')
                            ->label('Support Email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('support_phone')
                            ->label('Support Phone')
                            ->tel()
                            ->maxLength(50),
                        Textarea::make('office_address')
                            ->label('Office Address')
                            ->rows(3),
                    ])
                    ->columns(2),
                Section::make('Availability')
                    ->components([
                        Toggle::make('maintenance_mode')
                            ->label('Maintenance Mode')
                            ->helperText('When enabled, the public site can show a maintenance notice.')
                            ->inline(false),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::setMany($data);

        Notification::make()
            ->title('Site settings saved')
            ->success()
            ->send();
    }
}
