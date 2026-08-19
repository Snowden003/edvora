<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudentVerificationResource\Pages;
use App\Mail\StudentIdentityApproved;
use App\Mail\StudentIdentityRejected;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class StudentVerificationResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Student Verification';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $modelLabel = 'Student Verification';
    protected static ?string $pluralModelLabel = 'Student Verifications';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) User::where('role', 'student')
            ->where('identity_status', 'pending')
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'student')
            ->whereIn('identity_status', ['pending', 'approved', 'rejected'])
            ->whereNotNull('tazkira_image');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Verification Status')
                ->schema([
                    Forms\Components\Select::make('identity_status')
                        ->label('Status')
                        ->options([
                            'pending'  => 'Pending Review',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->disabled()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Infolists\Components\Section::make('Verification Status')
                ->icon('heroicon-o-shield-check')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('identity_status')
                        ->label('Status')
                        ->badge()
                        ->color(fn(string $state) => match($state) {
                            'pending'  => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default    => 'gray',
                        })
                        ->formatStateUsing(fn($state) => match($state) {
                            'pending'       => 'Pending Review',
                            'approved'      => 'Approved',
                            'rejected'      => 'Rejected',
                            'not_submitted' => 'Not Submitted',
                            default         => $state,
                        }),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Registered At')
                        ->dateTime('M d, Y - H:i'),
                ]),

            Infolists\Components\Section::make('Student Information')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('name')->label('Full Name'),
                    Infolists\Components\TextEntry::make('email')->label('Email'),
                    Infolists\Components\TextEntry::make('phone')->label('Phone')->default('—'),
                ]),

            Infolists\Components\Section::make('Tazkira (ID) Image')
                ->icon('heroicon-o-identification')
                ->schema([
                    Infolists\Components\ImageEntry::make('tazkira_image')
                        ->label('Uploaded Tazkira Image')
                        ->state(fn($record) => $record->tazkira_image
                            ? asset('storage/' . $record->tazkira_image)
                            : null)
                        ->height(300)
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Rejection Reason')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->visible(fn($record) => filled($record->identity_rejection_reason))
                ->schema([
                    Infolists\Components\TextEntry::make('identity_rejection_reason')
                        ->label('Reason')
                        ->columnSpanFull(),
                ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('tazkira_image')
                    ->label('Tazkira')
                    ->state(fn($record) => $record->tazkira_image
                        ? asset('storage/' . $record->tazkira_image)
                        : null)
                    ->height(48)
                    ->width(72),
                Tables\Columns\TextColumn::make('needs_review')
                    ->label('')
                    ->state(fn(User $record) => $record->identity_status === 'pending' ? '● Needs Review' : null)
                    ->color('danger')
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('identity_status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending'       => 'Pending',
                        'approved'      => 'Approved',
                        'rejected'      => 'Rejected',
                        'not_submitted' => 'Not Submitted',
                        default         => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                        'gray'    => 'not_submitted',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('identity_status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(User $record) => $record->identity_status !== 'approved')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Student Identity')
                    ->modalDescription('This will grant the student full access to the dashboard.')
                    ->action(function (User $record) {
                        $record->update(['identity_status' => 'approved', 'identity_rejection_reason' => null]);
                        $emailSent = true;
                        try {
                            Mail::to($record->email)->send(new StudentIdentityApproved($record));
                        } catch (\Exception $e) {
                            $emailSent = false;
                        }
                        Notification::make()
                            ->title('Identity Approved')
                            ->body($emailSent
                                ? "Student approved and notified at {$record->email}."
                                : 'Student approved, but email notification failed to send.')
                            ->color($emailSent ? 'success' : 'warning')
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(User $record) => $record->identity_status !== 'rejected')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason (shown to student)')
                            ->placeholder('e.g. The image is blurry or incomplete...')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->update([
                            'identity_status'            => 'rejected',
                            'identity_rejection_reason'  => $data['reason'],
                        ]);
                        $emailSent = true;
                        try {
                            Mail::to($record->email)->send(new StudentIdentityRejected($record));
                        } catch (\Exception $e) {
                            $emailSent = false;
                        }
                        Notification::make()
                            ->title('Identity Rejected')
                            ->body($emailSent
                                ? "Student notified of rejection at {$record->email}."
                                : 'Identity rejected, but email notification failed to send.')
                            ->color($emailSent ? 'danger' : 'warning')
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Selected Students')
                        ->modalDescription('All selected students will be approved and notified via email in the background.')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $count = 0;
                            $records->each(function (User $record) use (&$count) {
                                if ($record->identity_status !== 'approved') {
                                    $record->update(['identity_status' => 'approved', 'identity_rejection_reason' => null]);
                                    try {
                                        Mail::to($record->email)->queue(new StudentIdentityApproved($record));
                                    } catch (\Exception $e) {
                                        // queue dispatch failed, silently skip
                                    }
                                    $count++;
                                }
                            });
                            Notification::make()
                                ->title('Students Approved')
                                ->body("{$count} student(s) approved. Email notifications queued for delivery.")
                                ->color('success')
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Rejection Reason (shown to all selected students)')
                                ->placeholder('e.g. The submitted image is blurry or incomplete...')
                                ->required()
                                ->rows(3),
                        ])
                        ->modalHeading('Reject Selected Students')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records, array $data) {
                            $count = 0;
                            $records->each(function (User $record) use ($data, &$count) {
                                if ($record->identity_status !== 'rejected') {
                                    $record->update([
                                        'identity_status'           => 'rejected',
                                        'identity_rejection_reason' => $data['reason'],
                                    ]);
                                    try {
                                        Mail::to($record->email)->queue(new StudentIdentityRejected($record));
                                    } catch (\Exception $e) {
                                        // queue dispatch failed, silently skip
                                    }
                                    $count++;
                                }
                            });
                            Notification::make()
                                ->title('Students Rejected')
                                ->body("{$count} student(s) rejected. Email notifications queued for delivery.")
                                ->color('danger')
                                ->send();
                        }),
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
            'index' => Pages\ListStudentVerifications::route('/'),
            'view'  => Pages\ViewStudentVerification::route('/{record}/view'),
        ];
    }
}
