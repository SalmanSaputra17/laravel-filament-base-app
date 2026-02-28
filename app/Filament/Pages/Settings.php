<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page
{
    use InteractsWithForms;

    protected static ?string $title = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.settings';

    protected array $settings = [
        'app_name' => 'APP_NAME',
        'app_url' => 'APP_URL',
        'app_timezone' => 'APP_TIMEZONE',
        'app_locale' => 'APP_LOCALE',
    ];

    public function mount(): void
    {
        $this->form->fill([
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_timezone' => config('app.timezone'),
            'app_locale' => config('app.locale'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')
                    ->schema([
                        TextInput::make('app_name')
                            ->label('Application Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('app_url')
                            ->label('Application URL')
                            ->required()
                            ->url(),
                    ]),
                Section::make('Localization')
                    ->schema([
                        TextInput::make('app_timezone')
                            ->label('Timezone'),
                        TextInput::make('app_locale')
                            ->label('Locale'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $this->validate();

        $data = $this->form->getState();

        foreach ($this->settings as $formField => $envKey) {
            if (isset($data[$formField])) {
                $this->updateEnvFile($envKey, $data[$formField]);
            }
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    protected function updateEnvFile(string $key, string $value): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $value = match ($key) {
            'APP_NAME' => '"'.$value.'"',
            default => $value,
        };

        if (preg_match("/^{$key}=.*/m", $envContent)) {
            $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        } else {
            $envContent .= "\n{$key}={$value}";
        }

        file_put_contents($envPath, $envContent);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->submit('save'),
            Action::make('cancel')
                ->label('Cancel')
                ->url('/admin')
                ->color('gray'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check();
    }
}
