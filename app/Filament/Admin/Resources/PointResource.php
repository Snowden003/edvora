<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PointResource\Pages;
use App\Models\Point;
use App\Models\ScoringRule;
use App\Models\User;
use App\Services\ScoreService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PointResource extends Resource
{
    protected static ?string $model = Point::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Points & Scoring';
    protected static ?string $navigationGroup = 'Academic & Scoring';
    protected static ?string $modelLabel = 'Point Entry';
    protected static ?string $pluralModelLabel = 'Point Entries';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'points';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Student')
                    ->options(fn() => User::where('role', 'student')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('course_id')
                    ->label('Course (optional)')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options(fn() => array_merge(
                        ['manual' => 'Manual Adjustment'],
                        ScoringRule::active()
                            ->pluck('label', 'action_name')
                            ->unique()
                            ->sort()
                            ->toArray()
                    ))
                    ->default('manual')
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->required()
                    ->integer()
                    ->helperText('Positive numbers add points, negative numbers deduct points.'),

                Forms\Components\Textarea::make('reason')
                    ->label('Reason')
                    ->required()
                    ->rows(2)
                    ->placeholder('e.g. Late to class'),

                Forms\Components\Select::make('created_by')
                    ->label('Awarded by')
                    ->options(fn() => User::whereIn('role', ['admin', 'teacher'])->pluck('name', 'id'))
                    ->searchable()
                    ->default(fn() => auth()->id())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Points')
                    ->sortable()
                    ->numeric()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => ($state >= 0 ? '+' : '') . number_format($state)),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn($state) => ucwords(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('course.title')
                    ->label('Course')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Awarded By')
                    ->placeholder('System'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(fn() => ScoringRule::active()->pluck('label', 'action_name')->toArray()),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Student')
                    ->options(fn() => User::where('role', 'student')->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'course', 'creator']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPoints::route('/'),
            'create' => Pages\CreatePoint::route('/create'),
            'edit' => Pages\EditPoint::route('/{record}/edit'),
        ];
    }
}
