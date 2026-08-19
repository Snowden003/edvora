<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TeacherResource\Pages;
use App\Mail\AdminMessageToTeacher;
use App\Models\Category;
use App\Models\Notification as AppNotification;
use App\Models\User;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

class TeacherResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Teachers';

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $modelLabel = 'Teacher';

    protected static ?string $pluralModelLabel = 'Teachers';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'teachers';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'teacher')
            ->with([
                'teacher',
                'courses',
                'enrollments',
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&background=10B981&color=fff')
                    ->size(40),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied')
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('—')
                    ->icon('heroicon-m-phone'),

                TextColumn::make('teacher.specialization')
                    ->label('Specialization')
                    ->placeholder('—'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'banned',
                    ]),

                BadgeColumn::make('teacher.is_verified')
                    ->label('Verified')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->colors([
                        'success' => fn ($state) => $state,
                        'gray' => fn ($state) => ! $state,
                    ]),

                TextColumn::make('courses_count')
                    ->label('Courses')
                    ->counts('courses')
                    ->sortable()
                    ->icon('heroicon-m-book-open'),

                TextColumn::make('categories')
                    ->label('Categories')
                    ->getStateUsing(function (User $record): string {
                        return $record->courses()
                            ->with('category')
                            ->get()
                            ->pluck('category.name')
                            ->filter()
                            ->unique()
                            ->implode(', ') ?: '—';
                    })
                    ->placeholder('—')
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Account Status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'banned' => 'Banned',
                    ]),

                SelectFilter::make('category')
                    ->label('Category')
                    ->placeholder('All Categories')
                    ->options(fn () => Category::orderBy('name')->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (filled($data['value'])) {
                            $query->whereHas('courses', function (Builder $q) use ($data) {
                                $q->where('category_id', $data['value']);
                            });
                        }

                        return $query;
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Action::make('send_email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->form([
                        TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Important announcement'),

                        RichEditor::make('body')
                            ->label('Message')
                            ->required()
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->placeholder('Write your message here…'),
                    ])
                    ->action(function (User $record, array $data): void {
                        $sent = true;
                        try {
                            Mail::to($record->email)->send(
                                new AdminMessageToTeacher($record, $data['subject'], $data['body'])
                            );
                        } catch (\Exception $e) {
                            $sent = false;
                        }

                        Notification::make()
                            ->title($sent ? 'Email sent to '.$record->name : 'Failed to send email')
                            ->color($sent ? 'success' : 'danger')
                            ->send();
                    }),

                Action::make('send_notification')
                    ->label('Notify')
                    ->icon('heroicon-o-bell')
                    ->color('warning')
                    ->form([
                        TextInput::make('title')
                            ->label('Notification Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Course update'),

                        Textarea::make('message')
                            ->label('Message')
                            ->required()
                            ->rows(3)
                            ->placeholder('Write your notification message here…'),
                    ])
                    ->action(function (User $record, array $data): void {
                        AppNotification::create([
                            'user_id' => $record->id,
                            'type' => AppNotification::TYPE_ADMIN_MESSAGE,
                            'title' => $data['title'],
                            'message' => $data['message'],
                            'is_read' => false,
                        ]);

                        Notification::make()
                            ->title('Notification sent to '.$record->name)
                            ->success()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('bulk_email')
                        ->label('Send Email to Selected')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->form([
                            TextInput::make('subject')
                                ->label('Subject')
                                ->required()
                                ->maxLength(255),

                            RichEditor::make('body')
                                ->label('Message')
                                ->required()
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $sent = 0;
                            $failed = 0;
                            foreach ($records as $teacher) {
                                try {
                                    Mail::to($teacher->email)->send(
                                        new AdminMessageToTeacher($teacher, $data['subject'], $data['body'])
                                    );
                                    $sent++;
                                } catch (\Exception $e) {
                                    $failed++;
                                }
                            }

                            Notification::make()
                                ->title("Email sent to {$sent} teacher(s)".($failed ? ", {$failed} failed." : '.'))
                                ->color($failed === 0 ? 'success' : 'warning')
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('bulk_notify')
                        ->label('Send Notification to Selected')
                        ->icon('heroicon-o-bell')
                        ->color('warning')
                        ->form([
                            TextInput::make('title')
                                ->label('Title')
                                ->required()
                                ->maxLength(255),

                            Textarea::make('message')
                                ->label('Message')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $teacher) {
                                AppNotification::create([
                                    'user_id' => $teacher->id,
                                    'type' => AppNotification::TYPE_ADMIN_MESSAGE,
                                    'title' => $data['title'],
                                    'message' => $data['message'],
                                    'is_read' => false,
                                ]);
                            }

                            Notification::make()
                                ->title('Notification sent to '.$records->count().' teacher(s).')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('bulk_delete')
                        ->label('Delete Selected')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            foreach ($records as $teacher) {
                                $teacher->delete();
                            }

                            Notification::make()
                                ->title('Deleted '.$records->count().' teacher(s).')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'view' => Pages\ViewTeacher::route('/{record}'),
        ];
    }
}
