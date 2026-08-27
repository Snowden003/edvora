<?php

namespace App\Filament\Admin\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $navigationGroup = 'Settings & System';

    protected static ?int $navigationSort = 100;

    protected static ?string $title = 'Site Settings';

    protected static ?string $slug = 'site-settings';

    protected static string $view = 'filament.admin.pages.site-settings';

    // Form state
    public ?array $companyData = [];
    public ?array $socialData = [];
    public ?array $seoData = [];
    public ?array $footerData = [];

    public function mount(): void
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        $this->companyData = [
            'company_name'         => $settings['company_name'] ?? '',
            'company_tagline'      => $settings['company_tagline'] ?? '',
            'company_description'  => $settings['company_description'] ?? '',
            'company_email'        => $settings['company_email'] ?? '',
            'company_phone'        => $settings['company_phone'] ?? '',
            'company_address'      => $settings['company_address'] ?? '',
            'company_founded_year' => $settings['company_founded_year'] ?? '',
        ];

        $this->socialData = [
            'social_facebook'  => $settings['social_facebook'] ?? '',
            'social_twitter'   => $settings['social_twitter'] ?? '',
            'social_instagram' => $settings['social_instagram'] ?? '',
            'social_linkedin'  => $settings['social_linkedin'] ?? '',
            'social_youtube'   => $settings['social_youtube'] ?? '',
        ];

        $this->seoData = [
            'seo_meta_title'       => $settings['seo_meta_title'] ?? '',
            'seo_meta_description' => $settings['seo_meta_description'] ?? '',
        ];

        $this->footerData = [
            'footer_copyright'      => $settings['footer_copyright'] ?? '',
            'footer_newsletter_text' => $settings['footer_newsletter_text'] ?? '',
        ];

        $this->companyForm->fill($this->companyData);
        $this->socialForm->fill($this->socialData);
        $this->seoForm->fill($this->seoData);
        $this->footerForm->fill($this->footerData);
    }

    protected function getForms(): array
    {
        return [
            'companyForm',
            'socialForm',
            'seoForm',
            'footerForm',
        ];
    }

    public function companyForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->icon('heroicon-o-building-office')
                    ->description('Basic company details displayed across the website.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('company_founded_year')
                            ->label('Founded Year')
                            ->maxLength(4),

                        Forms\Components\TextInput::make('company_tagline')
                            ->label('Tagline')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('company_description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('company_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('company_phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(50),

                        Forms\Components\Textarea::make('company_address')
                            ->label('Address')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('companyData');
    }

    public function socialForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Social Media Links')
                    ->icon('heroicon-o-share')
                    ->description('Social media profile URLs for header and footer.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('social_facebook')
                            ->label('Facebook URL')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(500),

                        Forms\Components\TextInput::make('social_twitter')
                            ->label('Twitter / X URL')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(500),

                        Forms\Components\TextInput::make('social_instagram')
                            ->label('Instagram URL')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(500),

                        Forms\Components\TextInput::make('social_linkedin')
                            ->label('LinkedIn URL')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(500),

                        Forms\Components\TextInput::make('social_youtube')
                            ->label('YouTube URL')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(500),
                    ]),
            ])
            ->statePath('socialData');
    }

    public function seoForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('SEO Settings')
                    ->icon('heroicon-o-magnifying-glass')
                    ->description('Default SEO meta tags for pages without custom values.')
                    ->schema([
                        Forms\Components\TextInput::make('seo_meta_title')
                            ->label('Default Meta Title')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('seo_meta_description')
                            ->label('Default Meta Description')
                            ->rows(3)
                            ->maxLength(500),
                    ]),
            ])
            ->statePath('seoData');
    }

    public function footerForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Footer Settings')
                    ->icon('heroicon-o-bars-3-bottom-left')
                    ->description('Footer text content.')
                    ->schema([
                        Forms\Components\TextInput::make('footer_copyright')
                            ->label('Copyright Text')
                            ->maxLength(500)
                            ->helperText('The year is auto-prepended (e.g. © 2026 [your text]).'),

                        Forms\Components\TextInput::make('footer_newsletter_text')
                            ->label('Newsletter Description')
                            ->maxLength(500),
                    ]),
            ])
            ->statePath('footerData');
    }

    public function saveCompany(): void
    {
        $data = $this->companyForm->getState();
        $this->saveSettings($data);

        Notification::make()
            ->title('Company settings saved')
            ->success()
            ->send();
    }

    public function saveSocial(): void
    {
        $data = $this->socialForm->getState();
        $this->saveSettings($data);

        Notification::make()
            ->title('Social media links saved')
            ->success()
            ->send();
    }

    public function saveSeo(): void
    {
        $data = $this->seoForm->getState();
        $this->saveSettings($data);

        Notification::make()
            ->title('SEO settings saved')
            ->success()
            ->send();
    }

    public function saveFooter(): void
    {
        $data = $this->footerForm->getState();
        $this->saveSettings($data);

        Notification::make()
            ->title('Footer settings saved')
            ->success()
            ->send();
    }

    private function saveSettings(array $data): void
    {
        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value ?? '');
        }
    }
}
