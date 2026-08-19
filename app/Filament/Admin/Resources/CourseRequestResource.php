<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CourseRequestResource\Pages;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseRequest;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseRequestResource extends Resource
{
    protected static ?string $model = CourseRequest::class;

    protected static ?string $navigationIcon    = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationLabel   = 'Course Requests';
    protected static ?string $navigationGroup   = 'Content Management';
    protected static ?int    $navigationSort     = 3;

    public static function getNavigationBadge(): ?string
    {
        return (string) CourseRequest::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Request Details')->columns(2)->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Course Title')->disabled()->columnSpanFull(),

                Forms\Components\Select::make('teacher_id')
                    ->label('Teacher')->disabled()
                    ->options(fn() => User::where('role','teacher')->pluck('name','id')),

                Forms\Components\Select::make('category_id')
                    ->label('Category')->disabled()
                    ->options(fn() => Category::pluck('name','id')),

                Forms\Components\Select::make('status')
                    ->label('Status')->required()
                    ->options(['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected']),

                Forms\Components\Textarea::make('admin_notes')
                    ->label('Admin Notes / Feedback')
                    ->rows(3)->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('Teacher Note')->disabled()->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Course Title')->searchable()->sortable()->limit(40),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Teacher')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')->badge()->sortable(),

                Tables\Columns\TextColumn::make('course.title')
                    ->label('Course')
                    ->placeholder('Course Deleted')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested At')->dateTime('M d, Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected']),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(CourseRequest $r) => $r->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Course Request')
                    ->modalDescription(fn (CourseRequest $r) => "Approve {$r->teacher->name}'s request to teach '{$r->title}'?")
                    ->modalSubmitActionLabel('Approve')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Feedback to Teacher (Optional)')->rows(2),
                    ])
                    ->action(function (CourseRequest $record, array $data) {
                        // Find existing course with same title and no teacher
                        $course = Course::where('title', $record->title)
                            ->whereNull('teacher_id')
                            ->first();

                        if (! $course) {
                            // Create new course from request
                            $course = Course::create([
                                'title'       => $record->title,
                                'category_id' => $record->category_id,
                                'teacher_id'  => $record->teacher_id,
                                'status'      => 'draft',
                                'slug'        => \Illuminate\Support\Str::slug($record->title) . '-' . uniqid(),
                            ]);
                        } else {
                            // Assign teacher to existing course
                            $course->update([
                                'teacher_id' => $record->teacher_id,
                            ]);
                        }

                        // Update the request
                        $record->update([
                            'status'      => 'approved',
                            'course_id'   => $course->id,
                            'admin_notes' => $data['admin_notes'] ?? null,
                        ]);

                        Notification::make()
                            ->title("Request approved! Teacher assigned to course '{$record->title}'")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(CourseRequest $r) => $r->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Reason for Rejection')->required()->rows(3),
                    ])
                    ->action(function (CourseRequest $record, array $data) {
                        $record->update([
                            'status'      => 'rejected',
                            'admin_notes' => $data['admin_notes'],
                        ]);
                        Notification::make()->title('Request rejected.')->warning()->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCourseRequests::route('/'),
            'create' => Pages\CreateCourseRequest::route('/create'),
            'edit'   => Pages\EditCourseRequest::route('/{record}/edit'),
        ];
    }
}
