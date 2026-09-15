<?php

namespace App\Filament\Admin\Resources;

use Closure;
use App\Filament\Admin\Resources\CourseResource\Pages;
use App\Filament\Admin\Resources\CourseResource\RelationManagers;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Services\CurriculumSpreadsheetImporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Courses';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?string $modelLabel = 'Course';
    protected static ?string $pluralModelLabel = 'Courses';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Basic Information')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Course Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, Forms\Set $set) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Auto-generated from title. Must be unique.'),

                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(fn() => Category::pluck('name', 'id')->toArray())
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Category Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(Category::class)
                                    ->maxLength(255)
                                    ->helperText('Auto-generated from name'),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->maxLength(500)
                                    ->rows(2),
                                Forms\Components\TextInput::make('icon')
                                    ->label('Icon (Heroicon)')
                                    ->placeholder('e.g. book-open, academic-cap, computer-desktop')
                                    ->helperText('Enter a Heroicon name. See heroicons.com for available icons.'),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $category = Category::create($data);
                                return $category->id;
                            }),

                        Forms\Components\Select::make('teacher_id')
                            ->label('Instructor (Teacher)')
                            ->nullable()
                            ->live()
                            ->searchable()
                            ->preload()
                            ->placeholder('No teacher assigned yet')
                            ->options(fn() => User::where('role', 'teacher')->where('status', 'active')->pluck('name', 'id')->toArray())
                            ->helperText('Select an instructor before publishing this course. Courses without an instructor must remain drafts.'),

                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Course Thumbnail')
                            ->image()
                            ->directory('courses/thumbnails')
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Course Description')
                            ->required()
                            ->columnSpanFull()
                            ->rows(5)
                            ->maxLength(1000)
                            ->helperText(fn($state) => 'Max 1000 characters. Current: ' . strlen($state ?? '')),
                    ]),

                Forms\Components\Section::make('Course Details')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('level')
                            ->label('Difficulty Level')
                            ->required()
                            ->options([
                                'beginner' => 'Beginner',
                                'intermediate' => 'Intermediate',
                                'advanced' => 'Advanced',
                            ])
                            ->default('beginner'),

                        Forms\Components\TextInput::make('duration_hours')
                            ->label('Duration (Hours)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(10)
                            ->suffix('hours'),

                        Forms\Components\Select::make('status')
                            ->label('Course Status')
                            ->required()
                            ->options([
                                'draft'     => 'Draft (visible to teachers for requests)',
                                'published' => 'Published (visible to students)',
                                'started'   => 'Started (in progress)',
                                'completed' => 'Completed',
                                'archived'  => 'Archived',
                            ])
                            ->disableOptionWhen(fn (string $value, Forms\Get $get): bool => in_array($value, ['published', 'started'], true) && blank($get('teacher_id')))
                            ->rules([
                                fn (Forms\Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                    if (in_array($value, ['published', 'started'], true) && blank($get('teacher_id'))) {
                                        $fail('Select an instructor before publishing or starting this course.');
                                    }
                                },
                            ])
                            ->default('draft')
                            ->helperText(fn (Forms\Get $get): string => blank($get('teacher_id'))
                                ? 'Select an instructor to enable publishing or starting this course.'
                                : 'This course can be published or started because an instructor is assigned.'),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->helperText('When the course begins')
                            ->native(false),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->helperText('When the course ends')
                            ->native(false)
                            ->after('start_date'),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('دوره ویژه (Featured / VIP Course)')
                            ->helperText('فعال‌سازی استایل و طراحی ویژه (VIP)، حاشیه درخشان طلایی و برچسب اختصاصی در سراسر وب‌سایت.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Enrollment Settings')
                    ->icon('heroicon-o-users')
                    ->description('Configure minimum and maximum students, and auto-start behavior')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('min_students')
                            ->label('Minimum Students')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('No minimum')
                            ->helperText('Minimum students required for the course to start'),

                        Forms\Components\TextInput::make('max_students')
                            ->label('Maximum Students')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('No maximum')
                            ->helperText('Maximum students allowed to enroll (optional)'),

                        Forms\Components\Toggle::make('auto_start_enabled')
                            ->label('Auto-start when minimum reached')
                            ->helperText('Automatically start the course when minimum students are enrolled')
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('auto_start_info')
                            ->label('How Auto-start Works')
                            ->content(fn($get) => $get('auto_start_enabled')
                                ? 'When ' . ($get('min_students') ?? 'minimum') . ' students enroll, the course will automatically start. No admin action needed.'
                                : 'Auto-start is disabled. Admin must manually start the course.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Statistics (Read-only)')
                    ->icon('heroicon-o-chart-bar')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('enrolled_count')
                            ->label('Enrolled Students')
                            ->disabled()
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('rating')
                            ->label('Average Rating')
                            ->disabled()
                            ->numeric()
                            ->default(0)
                            ->step(0.01)
                            ->suffix('/ 5.00'),

                        Forms\Components\TextInput::make('total_reviews')
                            ->label('Total Reviews')
                            ->disabled()
                            ->numeric()
                            ->default(0),
                    ])
                    ->hidden(fn($operation) => $operation === 'create'),

                Forms\Components\Section::make('Import Course Curriculum')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->description('Upload an Excel file to add lessons to the curriculum automatically.')
                    ->schema([
                        Forms\Components\FileUpload::make('curriculum_spreadsheet')
                            ->label('Curriculum Excel File')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->helperText('Use the first sheet with these headers: Lesson Title, Duration, Description. Duration is in minutes.')
                            ->maxSize(5120)
                            ->storeFiles(false)
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (! $state instanceof TemporaryUploadedFile) {
                                    return;
                                }

                                try {
                                    $lessons = app(CurriculumSpreadsheetImporter::class)->import($state->getRealPath());
                                    $set('lessons', $lessons);

                                    Notification::make()
                                        ->success()
                                        ->title('Curriculum imported')
                                        ->body(count($lessons) . ' lessons are ready to review and save.')
                                        ->send();
                                } catch (RuntimeException $exception) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Curriculum import failed')
                                        ->body($exception->getMessage())
                                        ->persistent()
                                        ->send();
                                }
                            })
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Course Curriculum (Lessons)')
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        Forms\Components\Repeater::make('lessons')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    Forms\Components\TextInput::make('order')
                                        ->label('#')
                                        ->numeric()
                                        ->default(fn(Forms\Get $get, $context) => count($get('../../lessons') ?? []) + 1)
                                        ->columnSpan(1),

                                    Forms\Components\TextInput::make('title')
                                        ->label('Lesson Title')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(6),

                                    Forms\Components\TextInput::make('duration_minutes')
                                        ->label('Duration')
                                        ->numeric()
                                        ->minValue(1)
                                        ->default(30)
                                        ->suffix('min')
                                        ->columnSpan(2),

                                ]),

                                Forms\Components\Textarea::make('description')
                                    ->label('Short Description')
                                    ->rows(2)
                                    ->maxLength(500),
                            ])
                            ->itemLabel(fn(array $state) => ($state['order'] ?? 0) . '. ' . ($state['title'] ?? 'New Lesson'))
                            ->addActionLabel('Add Lesson')
                            ->reorderableWithDragAndDrop()
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->defaultItems(0)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                // Auto-renumber all lessons when items change
                                $lessons = $get('lessons') ?? [];
                                $index = 1;
                                foreach ($lessons as $key => $lesson) {
                                    $set("lessons.{$key}.order", $index++);
                                }
                            }),
                    ]),

                Forms\Components\Section::make('Free Course Benefits')
                    ->icon('heroicon-o-academic-cap')
                    ->description('Every Edvora course is free. Select only the learning benefits available for this course.')
                    ->schema([
                        Forms\Components\Placeholder::make('free_access')
                            ->label('Learner Access')
                            ->content('Free for every learner')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\Toggle::make('has_certificate')
                                ->label('Certificate of Completion'),

                            Forms\Components\Toggle::make('has_lifetime_access')
                                ->label('Lifetime Access'),

                            Forms\Components\Toggle::make('has_mobile_access')
                                ->label('Mobile & Desktop Access'),

                            Forms\Components\Toggle::make('has_downloadable_resources')
                                ->label('Downloadable Resources'),

                            Forms\Components\Toggle::make('has_community_access')
                                ->label('Community Access'),
                        ]),
                    ]),

                Forms\Components\Section::make('Course Information Badges')
                    ->icon('heroicon-o-tag')
                    ->description('Choose the free-course details shown in the course information sidebar.')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\Toggle::make('show_category_badge')
                                ->label('Show Category Badge'),

                            Forms\Components\Toggle::make('show_level_badge')
                                ->label('Show Level Badge'),

                            Forms\Components\Toggle::make('show_duration_badge')
                                ->label('Show Duration Badge'),

                            Forms\Components\Toggle::make('show_certificate_badge')
                                ->label('Show Certificate Badge'),

                            Forms\Components\Toggle::make('show_students_badge')
                                ->label('Show Students Count Badge'),
                        ]),
                    ]),

                Forms\Components\Section::make('Class Schedule')
                    ->icon('heroicon-o-clock')
                    ->description('Set the primary and backup class time for this course. The teacher must attend the secondary time if they cannot make the primary.')
                    ->columns(1)
                    ->schema([

                        Forms\Components\Fieldset::make('🕐 Primary Class Time')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TimePicker::make('primary_class_start')
                                    ->label('Start Time')
                                    ->seconds(false)
                                    ->displayFormat('H:i'),

                                Forms\Components\TimePicker::make('primary_class_end')
                                    ->label('End Time')
                                    ->seconds(false)
                                    ->displayFormat('H:i')
                                    ->after('primary_class_start'),

                                Forms\Components\CheckboxList::make('primary_class_days')
                                    ->label('Class Days')
                                    ->options([
                                        'saturday'  => 'Saturday',
                                        'sunday'    => 'Sunday',
                                        'monday'    => 'Monday',
                                        'tuesday'   => 'Tuesday',
                                        'wednesday' => 'Wednesday',
                                        'thursday'  => 'Thursday',
                                        'friday'    => 'Friday',
                                    ])
                                    ->columns(4)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('primary_class_note')
                                    ->label('Note (Primary)')
                                    ->placeholder('e.g. Main session — teacher must attend this time.')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Fieldset::make('🕑 Secondary (Backup) Class Time')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Placeholder::make('secondary_info')
                                    ->label('')
                                    ->content('If the teacher cannot attend the primary time, they are required to be present at this backup time.')
                                    ->columnSpanFull(),

                                Forms\Components\TimePicker::make('secondary_class_start')
                                    ->label('Start Time')
                                    ->seconds(false)
                                    ->displayFormat('H:i'),

                                Forms\Components\TimePicker::make('secondary_class_end')
                                    ->label('End Time')
                                    ->seconds(false)
                                    ->displayFormat('H:i')
                                    ->after('secondary_class_start'),

                                Forms\Components\CheckboxList::make('secondary_class_days')
                                    ->label('Class Days')
                                    ->options([
                                        'saturday'  => 'Saturday',
                                        'sunday'    => 'Sunday',
                                        'monday'    => 'Monday',
                                        'tuesday'   => 'Tuesday',
                                        'wednesday' => 'Wednesday',
                                        'thursday'  => 'Thursday',
                                        'friday'    => 'Friday',
                                    ])
                                    ->columns(4)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('secondary_class_note')
                                    ->label('Note (Secondary)')
                                    ->placeholder('e.g. Backup session — teacher must attend if primary is missed.')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->circular()
                    ->defaultImageUrl('https://via.placeholder.com/40'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Course Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Instructor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('level')
                    ->label('Level')
                    ->colors([
                        'success' => 'beginner',
                        'warning' => 'intermediate',
                        'danger' => 'advanced',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'published',
                        'warning' => 'started',
                        'info' => 'completed',
                        'danger' => 'archived',
                    ]),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),

                Tables\Columns\TextColumn::make('enrolled_count')
                    ->label('Students')
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format($state, 2)),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft (Open for Requests)',
                        'published' => 'Published (Waiting for Students)',
                        'started'   => 'Started (In Progress)',
                        'completed' => 'Completed',
                        'archived'  => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('level')
                    ->options([
                        'beginner' => 'Beginner',
                        'intermediate' => 'Intermediate',
                        'advanced' => 'Advanced',
                    ]),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(fn() => Category::pluck('name', 'id')->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['status' => 'published'])),
                    Tables\Actions\BulkAction::make('start')
                        ->label('Start Selected')
                        ->icon('heroicon-o-play-circle')
                        ->color('primary')
                        ->action(fn($records) => $records->each(fn($record) => $record->startCourse())),
                    Tables\Actions\BulkAction::make('archive')
                        ->label('Archive Selected')
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->action(fn($records) => $records->each->update(['status' => 'archived'])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
       {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
