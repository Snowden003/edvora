<?php

namespace App\Filament\Admin\Pages;

use App\Models\PageContent;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CompanyPagesManagement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Company Pages';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 15;

    protected static ?string $title = 'Company Pages Content';

    protected static ?string $slug = 'company-pages';

    protected static string $view = 'filament.admin.pages.company-pages-management';

    public ?array $aboutData = [];
    public ?array $storyData = [];
    public ?array $howWeWorkData = [];
    public ?array $termsData = [];
    public ?array $privacyData = [];

    public function mount(): void
    {
        $this->aboutData = PageContent::get('page_about', []);
        $this->storyData = PageContent::get('page_story', []);
        $this->howWeWorkData = PageContent::get('page_how_we_work', []);
        $this->termsData = PageContent::get('page_terms', []);
        $this->privacyData = PageContent::get('page_privacy', []);

        $this->aboutForm->fill($this->aboutData);
        $this->storyForm->fill($this->storyData);
        $this->howWeWorkForm->fill($this->howWeWorkData);
        $this->termsForm->fill($this->termsData);
        $this->privacyForm->fill($this->privacyData);
    }

    protected function getForms(): array
    {
        return [
            'aboutForm',
            'storyForm',
            'howWeWorkForm',
            'termsForm',
            'privacyForm',
        ];
    }

    // 1. ABOUT FORM
    public function aboutForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hero Section')
                    ->description('Top banner section of About page')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('hero.title')
                            ->label('Title Prefix')
                            ->required(),
                        Forms\Components\TextInput::make('hero.highlight')
                            ->label('Highlighted Word')
                            ->required(),
                        Forms\Components\TextInput::make('hero.subtitle')
                            ->label('Subtitle')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Mission & Vision')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('mission_vision.section_title')
                            ->label('Section Title')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('mission_vision.section_subtitle')
                            ->label('Section Subtitle')
                            ->columnSpan(1),

                        Forms\Components\Fieldset::make('Mission')
                            ->schema([
                                Forms\Components\TextInput::make('mission_vision.mission_title')->label('Mission Title'),
                                Forms\Components\TextInput::make('mission_vision.mission_icon')->label('Icon (Bootstrap Icon class e.g. bi-bullseye)'),
                                Forms\Components\Textarea::make('mission_vision.mission_text')->label('Mission Description')->rows(3)->columnSpanFull(),
                            ]),

                        Forms\Components\Fieldset::make('Vision')
                            ->schema([
                                Forms\Components\TextInput::make('mission_vision.vision_title')->label('Vision Title'),
                                Forms\Components\TextInput::make('mission_vision.vision_icon')->label('Icon (Bootstrap Icon class e.g. bi-eye)'),
                                Forms\Components\Textarea::make('mission_vision.vision_text')->label('Vision Description')->rows(3)->columnSpanFull(),
                            ]),
                    ]),

                Forms\Components\Section::make('Journey / Timeline Milestones')
                    ->schema([
                        Forms\Components\TextInput::make('timeline.section_title')->label('Section Title'),
                        Forms\Components\TextInput::make('timeline.section_subtitle')->label('Section Subtitle'),

                        Forms\Components\Repeater::make('timeline.items')
                            ->label('Timeline Milestones')
                            ->schema([
                                Forms\Components\TextInput::make('year')->label('Year')->required(),
                                Forms\Components\TextInput::make('title')->label('Milestone Title')->required(),
                                Forms\Components\Select::make('color')
                                    ->label('Color Theme')
                                    ->options([
                                        'blue' => 'Blue',
                                        'yellow' => 'Yellow',
                                        'green' => 'Green',
                                        'purple' => 'Purple',
                                    ])
                                    ->default('blue'),
                                Forms\Components\TextInput::make('icon')->label('Icon Class (e.g. bi-lightbulb-fill)'),
                                Forms\Components\Textarea::make('description')->label('Description')->rows(2)->columnSpanFull(),
                            ])
                            ->columns(4)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => ($state['year'] ?? '') . ' - ' . ($state['title'] ?? 'Milestone')),
                    ]),

                Forms\Components\Section::make('Team Members')
                    ->schema([
                        Forms\Components\TextInput::make('team.section_title')->label('Section Title'),
                        Forms\Components\TextInput::make('team.section_subtitle')->label('Section Subtitle'),

                        Forms\Components\Repeater::make('team.members')
                            ->label('Team Members')
                            ->schema([
                                Forms\Components\TextInput::make('name')->label('Full Name')->required(),
                                Forms\Components\TextInput::make('role')->label('Role / Position')->required(),
                                Forms\Components\Select::make('category')
                                    ->label('Category Filter')
                                    ->options([
                                        'tech' => 'Technology',
                                        'marketing' => 'Marketing',
                                        'creative' => 'Creative & Design',
                                    ])
                                    ->default('tech'),
                                Forms\Components\TextInput::make('icon')->label('Profile Icon (e.g. bi-code-slash)'),
                                Forms\Components\Textarea::make('bio')->label('Short Bio')->rows(2)->columnSpanFull(),
                                Forms\Components\TextInput::make('linkedin')->label('LinkedIn URL'),
                                Forms\Components\TextInput::make('twitter')->label('Twitter URL'),
                                Forms\Components\TextInput::make('github')->label('GitHub / Portfolio URL'),
                            ])
                            ->columns(4)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => ($state['name'] ?? '') . ' (' . ($state['role'] ?? '') . ')'),
                    ]),

                Forms\Components\Section::make('Call To Action Banner')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('cta.title')->label('CTA Title'),
                        Forms\Components\TextInput::make('cta.subtitle')->label('CTA Subtitle'),
                        Forms\Components\TextInput::make('cta.button_text')->label('Button Text'),
                        Forms\Components\TextInput::make('cta.button_url')->label('Button URL'),
                    ]),
            ])
            ->statePath('aboutData');
    }

    // 2. STORY FORM
    public function storyForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hero Section')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('hero.badge')->label('Badge Text'),
                        Forms\Components\TextInput::make('hero.title_prefix')->label('Title Prefix'),
                        Forms\Components\TextInput::make('hero.highlight')->label('Highlighted Word'),
                        Forms\Components\TextInput::make('hero.subtitle')->label('Subtitle')->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('The Genesis')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('genesis.title')->label('Title'),
                        Forms\Components\TextInput::make('genesis.icon')->label('Icon Class (e.g. bi-lightbulb)'),
                        Forms\Components\Textarea::make('genesis.text')->label('Content Paragraph')->rows(4)->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('The Vision')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('vision.title')->label('Title'),
                        Forms\Components\TextInput::make('vision.icon')->label('Icon Class (e.g. bi-eye)'),
                        Forms\Components\Textarea::make('vision.text')->label('Content Paragraph')->rows(4)->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('The Edvora Team Story')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('team_story.title')->label('Title'),
                        Forms\Components\TextInput::make('team_story.icon')->label('Icon Class (e.g. bi-people-fill)'),
                        Forms\Components\Textarea::make('team_story.paragraph_1')->label('Paragraph 1')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('team_story.paragraph_2')->label('Paragraph 2')->rows(3)->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Our Mission Today')
                    ->schema([
                        Forms\Components\TextInput::make('mission_today.title')->label('Title'),
                        Forms\Components\TextInput::make('mission_today.icon')->label('Icon Class (e.g. bi-mortarboard)'),
                        Forms\Components\Textarea::make('mission_today.text')->label('Description')->rows(4),
                        Forms\Components\Repeater::make('mission_today.stats')
                            ->label('Highlight Statistics')
                            ->schema([
                                Forms\Components\TextInput::make('value')->label('Value (e.g. 10K+)')->required(),
                                Forms\Components\TextInput::make('label')->label('Label (e.g. Students Empowered)')->required(),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->statePath('storyData');
    }

    // 3. HOW WE WORK FORM
    public function howWeWorkForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hero Section')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('hero.badge')->label('Badge Text'),
                        Forms\Components\TextInput::make('hero.title_prefix')->label('Title Prefix'),
                        Forms\Components\TextInput::make('hero.highlight')->label('Highlighted Word'),
                        Forms\Components\TextInput::make('hero.subtitle')->label('Subtitle')->columnSpanFull(),
                        Forms\Components\TextInput::make('hero.cta_text')->label('Hero CTA Text'),
                        Forms\Components\TextInput::make('hero.cta_url')->label('Hero CTA URL'),
                    ]),

                Forms\Components\Section::make('Non-Profit Mission Statement')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('mission.title')->label('Title'),
                        Forms\Components\Textarea::make('mission.description')->label('Description')->rows(3)->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Core Values')
                    ->schema([
                        Forms\Components\Repeater::make('values')
                            ->label('Value Cards')
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('Value Title')->required(),
                                Forms\Components\TextInput::make('icon')->label('Icon Class (e.g. bi-people-fill)'),
                                Forms\Components\Select::make('color')
                                    ->label('Accent Color')
                                    ->options([
                                        'blue' => 'Blue',
                                        'orange' => 'Orange',
                                        'purple' => 'Purple',
                                        'green' => 'Green',
                                    ])
                                    ->default('blue'),
                                Forms\Components\Textarea::make('description')->label('Description')->rows(2)->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Value Card'),
                    ]),

                Forms\Components\Section::make('Operational Pillars (How We Operate)')
                    ->schema([
                        Forms\Components\Repeater::make('pillars')
                            ->label('Operational Pillars')
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('Pillar Title')->required(),
                                Forms\Components\TextInput::make('subtitle')->label('Pillar Subtitle'),
                                Forms\Components\TextInput::make('icon')->label('Icon Class (e.g. bi-gear-fill)'),
                                Forms\Components\Textarea::make('description')->label('Overview Paragraph')->rows(3)->columnSpanFull(),

                                Forms\Components\Repeater::make('bullets')
                                    ->label('Key Features / Bullet Points')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')->label('Point Title')->required(),
                                        Forms\Components\TextInput::make('desc')->label('Point Description')->required(),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Pillar'),
                    ]),

                Forms\Components\Section::make('Global Impact Section Header')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('impact_section.title')->label('Section Title'),
                        Forms\Components\TextInput::make('impact_section.subtitle')->label('Section Subtitle'),
                    ]),

                Forms\Components\Section::make('Join Movement CTA')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('cta.title')->label('CTA Title'),
                        Forms\Components\TextInput::make('cta.description')->label('CTA Description'),
                        Forms\Components\TextInput::make('cta.button_text')->label('Button Text'),
                        Forms\Components\TextInput::make('cta.button_url')->label('Button URL'),
                    ]),
            ])
            ->statePath('howWeWorkData');
    }

    // 4. TERMS FORM
    public function termsForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hero Section')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('hero.badge')->label('Badge Text'),
                        Forms\Components\TextInput::make('hero.title')->label('Title'),
                        Forms\Components\TextInput::make('hero.last_updated')->label('Last Updated Text'),
                        Forms\Components\TextInput::make('hero.version')->label('Version Number'),
                        Forms\Components\Textarea::make('hero.subtitle')->label('Subtitle')->rows(2)->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Terms Modules (Bento Grid)')
                    ->schema([
                        Forms\Components\Repeater::make('modules')
                            ->label('Terms Articles & Modules')
                            ->schema([
                                Forms\Components\TextInput::make('number')->label('Article Number')->required(),
                                Forms\Components\TextInput::make('title')->label('Article Title')->required(),
                                Forms\Components\TextInput::make('icon')->label('Icon Class (e.g. bi-check2-circle)'),
                                Forms\Components\Select::make('size')
                                    ->label('Card Size')
                                    ->options([
                                        'normal' => 'Normal',
                                        'wide' => 'Wide (2 columns)',
                                        'large' => 'Large (Full width)',
                                        'tall' => 'Tall',
                                        'dark' => 'Dark Accent',
                                    ])
                                    ->default('normal'),
                                Forms\Components\Select::make('color')
                                    ->label('Color Line')
                                    ->options([
                                        'blue' => 'Blue',
                                        'teal' => 'Teal',
                                        'violet' => 'Violet',
                                        'amber' => 'Amber',
                                        'dark' => 'Dark',
                                    ])
                                    ->default('blue'),
                                Forms\Components\Textarea::make('text')->label('Full Legal Text')->rows(3)->columnSpanFull(),
                                Forms\Components\TextInput::make('tldr')->label('TL;DR Summary (Plain English note)')->columnSpanFull(),
                            ])
                            ->columns(5)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => ($state['number'] ?? '') . '. ' . ($state['title'] ?? 'Module')),
                    ]),

                Forms\Components\Section::make('Support & Legal Inquiries Box')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('support.title')->label('Box Title'),
                        Forms\Components\TextInput::make('support.link_text')->label('Link Text'),
                        Forms\Components\TextInput::make('support.link_url')->label('Link URL'),
                        Forms\Components\Textarea::make('support.description')->label('Description')->rows(2)->columnSpanFull(),
                    ]),
            ])
            ->statePath('termsData');
    }

    // 5. PRIVACY FORM
    public function privacyForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hero Section')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('hero.tag')->label('Tag / Badge Text'),
                        Forms\Components\TextInput::make('hero.heading_1')->label('Heading Line 1'),
                        Forms\Components\TextInput::make('hero.heading_2')->label('Heading Line 2'),
                        Forms\Components\TextInput::make('hero.protocol_button_text')->label('Protocol Button Text'),
                        Forms\Components\Textarea::make('hero.subtitle')->label('Subtitle')->rows(2)->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Privacy Protocol Panes')
                    ->schema([
                        Forms\Components\Repeater::make('panes')
                            ->label('Privacy Policy Panes')
                            ->schema([
                                Forms\Components\TextInput::make('index')->label('Index (e.g. 01)')->required(),
                                Forms\Components\TextInput::make('title')->label('Pane Title')->required(),
                                Forms\Components\Select::make('alignment')
                                    ->label('Card Alignment')
                                    ->options([
                                        'left' => 'Offset Left',
                                        'right' => 'Offset Right',
                                    ])
                                    ->default('left'),
                                Forms\Components\TagsInput::make('tags')
                                    ->label('Highlight Tags (e.g. GDPR Ready)')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('text')->label('Policy Description')->rows(3)->columnSpanFull(),
                                Forms\Components\TextInput::make('sub_note')->label('Footer Note (e.g. No data sold)')->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => ($state['index'] ?? '') . ' - ' . ($state['title'] ?? 'Pane')),
                    ]),
            ])
            ->statePath('privacyData');
    }

    // Save Handlers
    public function saveAbout(): void
    {
        $data = $this->aboutForm->getState();
        PageContent::set('page_about', $data, 'About Us Page Content');

        Notification::make()
            ->title('About Us page updated successfully!')
            ->success()
            ->send();
    }

    public function saveStory(): void
    {
        $data = $this->storyForm->getState();
        PageContent::set('page_story', $data, 'Our Story Page Content');

        Notification::make()
            ->title('Our Story page updated successfully!')
            ->success()
            ->send();
    }

    public function saveHowWeWork(): void
    {
        $data = $this->howWeWorkForm->getState();
        PageContent::set('page_how_we_work', $data, 'How We Work Page Content');

        Notification::make()
            ->title('How We Work page updated successfully!')
            ->success()
            ->send();
    }

    public function saveTerms(): void
    {
        $data = $this->termsForm->getState();
        PageContent::set('page_terms', $data, 'Terms of Service Page Content');

        Notification::make()
            ->title('Terms of Service page updated successfully!')
            ->success()
            ->send();
    }

    public function savePrivacy(): void
    {
        $data = $this->privacyForm->getState();
        PageContent::set('page_privacy', $data, 'Privacy Policy Page Content');

        Notification::make()
            ->title('Privacy Policy page updated successfully!')
            ->success()
            ->send();
    }
}
