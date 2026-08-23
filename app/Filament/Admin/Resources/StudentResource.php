<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudentResource\Pages;
use App\Mail\AdminMessageToStudent;
use App\Models\Notification as AppNotification;
use App\Models\User;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Students';
    protected static ?string $navigationGroup = 'Users';
    protected static ?string $modelLabel = 'Student';
    protected static ?string $pluralModelLabel = 'Students';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'students';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'student')
            ->with([
                'studentProfile',
                'enrollments.course',
                'achievements',
                'activities',
                'certificates',
                'completedLessons',
                'wishlists',
                'eventRegistrations',
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=1F8FFF&color=fff')
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

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Account')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger'  => 'banned',
                    ]),

                TextColumn::make('enrollments_count')
                    ->label('Courses')
                    ->counts('enrollments')
                    ->sortable()
                    ->icon('heroicon-m-book-open'),

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
                        'active'  => 'Active',
                        'pending' => 'Pending',
                        'banned'  => 'Banned',
                    ]),
            ])
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
                                new AdminMessageToStudent($record, $data['subject'], $data['body'])
                            );
                        } catch (\Exception $e) {
                            $sent = false;
                        }

                        Notification::make()
                            ->title($sent ? 'Email sent to ' . $record->name : 'Failed to send email')
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
                            'type'    => AppNotification::TYPE_ADMIN_MESSAGE,
                            'title'   => $data['title'],
                            'message' => $data['message'],
                            'is_read' => false,
                        ]);

                        Notification::make()
                            ->title('Notification sent to ' . $record->name)
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('bulk_activate')
                        ->label('Activate Selected / تایید و فعال‌سازی')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Activate Selected Students')
                        ->modalDescription('Are you sure you want to activate/approve the selected students?')
                        ->modalSubmitActionLabel('Yes, activate')
                        ->action(function (Collection $records): void {
                            $records->each(fn (User $user) => $user->update(['status' => 'active']));
                            Notification::make()
                                ->title($records->count() . ' student(s) activated successfully.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('bulk_ban')
                        ->label('Ban / Reject Selected / رد و مسدودسازی')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Ban Selected Students')
                        ->modalDescription('Are you sure you want to ban/reject the selected student accounts?')
                        ->modalSubmitActionLabel('Yes, ban')
                        ->action(function (Collection $records): void {
                            $records->each(fn (User $user) => $user->update(['status' => 'banned']));
                            Notification::make()
                                ->title($records->count() . ' student(s) banned successfully.')
                                ->danger()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('bulk_pending')
                        ->label('Set as Pending / تبدیل به در انتظار')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(fn (User $user) => $user->update(['status' => 'pending']));
                            Notification::make()
                                ->title($records->count() . ' student(s) set to pending.')
                                ->warning()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

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
                            $sent   = 0;
                            $failed = 0;
                            foreach ($records as $student) {
                                try {
                                    Mail::to($student->email)->send(
                                        new AdminMessageToStudent($student, $data['subject'], $data['body'])
                                    );
                                    $sent++;
                                } catch (\Exception $e) {
                                    $failed++;
                                }
                            }

                            Notification::make()
                                ->title("Email sent to {$sent} student(s)" . ($failed ? ", {$failed} failed." : '.'))
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
                            foreach ($records as $student) {
                                AppNotification::create([
                                    'user_id' => $student->id,
                                    'type'    => AppNotification::TYPE_ADMIN_MESSAGE,
                                    'title'   => $data['title'],
                                    'message' => $data['message'],
                                    'is_read' => false,
                                ]);
                            }

                            Notification::make()
                                ->title('Notification sent to ' . $records->count() . ' student(s).')
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
            'index' => Pages\ListStudents::route('/'),
            'view'  => Pages\ViewStudent::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }
}
