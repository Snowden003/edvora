<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TeacherApplicationResource\Pages;
use App\Mail\TeacherApplicationApproved;
use App\Models\Teacher;
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

class TeacherApplicationResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Teacher Applications';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $modelLabel = 'Teacher Application';
    protected static ?string $pluralModelLabel = 'Teacher Applications';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) User::where('role', 'teacher')->whereHas('teacher')->where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'teacher')
            ->whereHas('teacher');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Application Status')
                ->icon('heroicon-o-clipboard-document-check')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Current Status')
                        ->options([
                            'pending'   => 'Pending Review',
                            'active'    => 'Approved',
                            'rejected'  => 'Rejected',
                            'suspended' => 'Suspended',
                        ])
                        ->disabled()
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Personal Information')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Full Name')
                        ->disabled(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email Address')
                        ->disabled(),
                    Forms\Components\TextInput::make('phone')
                        ->label('Phone Number')
                        ->disabled(),
                    Forms\Components\TextInput::make('department')
                        ->label('Department / Field')
                        ->disabled(),
                    Forms\Components\Textarea::make('bio')
                        ->label('Biography')
                        ->disabled()
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Profile Photo & CV')
                ->icon('heroicon-o-paper-clip')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('avatar')
                        ->label('Profile Photo URL')
                        ->disabled()
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('view_avatar')
                                ->icon('heroicon-o-eye')
                                ->url(fn($state) => $state ?: null)
                                ->openUrlInNewTab()
                                ->visible(fn($state) => filled($state))
                        ),
                    Forms\Components\TextInput::make('cv_path')
                        ->label('CV / Resume')
                        ->disabled()
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('download_cv')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->label('Download')
                                ->url(fn($state) => $state ? asset('storage/' . $state) : null)
                                ->openUrlInNewTab()
                                ->visible(fn($state) => filled($state))
                        ),
                ]),

            Forms\Components\Section::make('Professional Details')
                ->icon('heroicon-o-academic-cap')
                ->columns(2)
                ->relationship('teacher')
                ->schema([
                    Forms\Components\TextInput::make('specialization')
                        ->label('Specialization')
                        ->disabled(),
                    Forms\Components\TextInput::make('years_of_experience')
                        ->label('Years of Experience')
                        ->disabled()
                        ->suffix('years'),
                    Forms\Components\Textarea::make('expertise')
                        ->label('Areas of Expertise')
                        ->disabled()
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Social & Online Presence')
                ->icon('heroicon-o-globe-alt')
                ->columns(3)
                ->relationship('teacher')
                ->schema([
                    Forms\Components\TextInput::make('linkedin')
                        ->label('LinkedIn')
                        ->disabled()
                        ->url()
                        ->suffixIcon('heroicon-o-arrow-top-right-on-square'),
                    Forms\Components\TextInput::make('github')
                        ->label('GitHub')
                        ->disabled()
                        ->url()
                        ->suffixIcon('heroicon-o-arrow-top-right-on-square'),
                    Forms\Components\TextInput::make('website')
                        ->label('Personal Website')
                        ->disabled()
                        ->url()
                        ->suffixIcon('heroicon-o-arrow-top-right-on-square'),
                ]),

        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Infolists\Components\Section::make('Application Status')
                ->icon('heroicon-o-clipboard-document-check')
                ->schema([
                    Infolists\Components\TextEntry::make('status')
                        ->label('Current Status')
                        ->badge()
                        ->color(fn(string $state) => match($state) {
                            'pending'   => 'warning',
                            'active'    => 'success',
                            'rejected'  => 'danger',
                            'suspended' => 'gray',
                            default     => 'secondary',
                        }),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Applied At')
                        ->dateTime('M d, Y - H:i'),
                ])->columns(2),

            Infolists\Components\Section::make('Personal Information')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('name')->label('Full Name'),
                    Infolists\Components\TextEntry::make('email')->label('Email Address'),
                    Infolists\Components\TextEntry::make('phone')->label('Phone Number')->default('—'),
                    Infolists\Components\TextEntry::make('department')->label('Department / Field')->default('—'),
                    Infolists\Components\TextEntry::make('experience_years')->label('Experience Years')->default('—'),
                    Infolists\Components\TextEntry::make('bio')
                        ->label('Biography')
                        ->columnSpanFull()
                        ->default('—'),
                ]),

            Infolists\Components\Section::make('Profile Photo & CV')
                ->icon('heroicon-o-paper-clip')
                ->columns(2)
                ->schema([
                    Infolists\Components\ImageEntry::make('avatar')
                        ->label('Profile Photo')
                        ->state(fn($record) => $record->avatar
                            ? (str_starts_with($record->avatar, 'http')
                                ? $record->avatar
                                : asset('storage/' . $record->avatar))
                            : 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&size=200&background=1f8fff&color=fff'
                        )
                        ->circular()
                        ->height(120),
                    Infolists\Components\TextEntry::make('cv_path')
                        ->label('CV / Resume')
                        ->default('No CV uploaded')
                        ->formatStateUsing(fn($state) => $state ? basename($state) : 'No CV uploaded')
                        ->url(fn($record) => $record->cv_path ? asset('storage/' . $record->cv_path) : null)
                        ->openUrlInNewTab()
                        ->icon(fn($record) => $record->cv_path ? 'heroicon-o-arrow-down-tray' : null)
                        ->color(fn($record) => $record->cv_path ? 'primary' : 'gray'),
                ]),

            Infolists\Components\Section::make('Professional Details')
                ->icon('heroicon-o-academic-cap')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('teacher.specialization')
                        ->label('Specialization')->default('—'),
                    Infolists\Components\TextEntry::make('teacher.years_of_experience')
                        ->label('Years of Experience')
                        ->suffix(' years')
                        ->default('—'),
                    Infolists\Components\IconEntry::make('teacher.is_verified')
                        ->label('Verified')
                        ->boolean(),
                    Infolists\Components\TextEntry::make('teacher.expertise')
                        ->label('Areas of Expertise')
                        ->columnSpanFull()
                        ->default('—'),
                ]),

            Infolists\Components\Section::make('Social & Online Presence')
                ->icon('heroicon-o-globe-alt')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('teacher.linkedin')
                        ->label('LinkedIn')
                        ->default('—')
                        ->url(fn($record) => $record->teacher?->linkedin)
                        ->openUrlInNewTab()
                        ->icon(fn($record) => $record->teacher?->linkedin ? 'heroicon-o-arrow-top-right-on-square' : null),
                    Infolists\Components\TextEntry::make('teacher.github')
                        ->label('GitHub')
                        ->default('—')
                        ->url(fn($record) => $record->teacher?->github)
                        ->openUrlInNewTab()
                        ->icon(fn($record) => $record->teacher?->github ? 'heroicon-o-arrow-top-right-on-square' : null),
                    Infolists\Components\TextEntry::make('teacher.website')
                        ->label('Personal Website')
                        ->default('—')
                        ->url(fn($record) => $record->teacher?->website)
                        ->openUrlInNewTab()
                        ->icon(fn($record) => $record->teacher?->website ? 'heroicon-o-arrow-top-right-on-square' : null),
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
                Tables\Columns\TextColumn::make('teacher.specialization')
                    ->label('Specialization')
                    ->default('—'),
                Tables\Columns\TextColumn::make('teacher.years_of_experience')
                    ->label('Experience')
                    ->suffix(' yrs'),
                Tables\Columns\TextColumn::make('needs_review')
                    ->label('')
                    ->state(fn(User $record) => $record->status === 'pending' ? '● Needs Review' : null)
                    ->color('danger')
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'danger'  => 'rejected',
                        'gray'    => 'suspended',
                    ]),
                Tables\Columns\IconColumn::make('teacher.is_verified')
                    ->label('Verified')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied At')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'active'    => 'Approved',
                        'rejected'  => 'Rejected',
                        'suspended' => 'Suspended',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(User $record) => $record->status !== 'active')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update(['status' => 'active']);
                        $record->teacher?->update(['is_verified' => true]);
                        Mail::to($record->email)->send(new TeacherApplicationApproved($record));
                        Notification::make()
                            ->title('Teacher approved and notified by email.')
                            ->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(User $record) => $record->status !== 'rejected')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update(['status' => 'rejected']);
                        $record->teacher?->update(['is_verified' => false]);
                        Notification::make()
                            ->title('Teacher application rejected.')
                            ->danger()->send();
                    }),

                Tables\Actions\Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('warning')
                    ->visible(fn(User $record) => $record->status === 'active')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update(['status' => 'suspended']);
                        Notification::make()
                            ->title('Teacher account suspended.')
                            ->warning()->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function (User $record) {
                                $record->update(['status' => 'active']);
                                $record->teacher?->update(['is_verified' => true]);
                            });
                            Notification::make()->title('Selected teachers approved.')->success()->send();
                        }),

                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn(User $r) => $r->update(['status' => 'rejected']));
                            Notification::make()->title('Selected teachers rejected.')->danger()->send();
                        }),

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
            'index' => Pages\ListTeacherApplications::route('/'),
            'view'  => Pages\EditTeacherApplication::route('/{record}/view'),
        ];
    }
}
