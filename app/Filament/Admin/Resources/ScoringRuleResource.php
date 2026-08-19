<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ScoringRuleResource\Pages;
use App\Models\ScoringRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ScoringRuleResource extends Resource
{
    protected static ?string $model = ScoringRule::class;
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationLabel = 'Scoring Rules';
    protected static ?string $navigationGroup = 'Gamification';
    protected static ?string $modelLabel = 'Scoring Rule';
    protected static ?string $pluralModelLabel = 'Scoring Rules';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'scoring-rules';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action_name')
                    ->label('Action Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->regex('/^[a-z0-9_]+$/')
                    ->helperText('Lowercase letters, numbers and underscores only. Used by the system.'),

                Forms\Components\TextInput::make('label')
                    ->label('Display Label')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('type')
                    ->label('Category')
                    ->options([
                        'attendance' => 'Attendance',
                        'assignment' => 'Assignment',
                        'participation' => 'Participation',
                        'manual' => 'Manual',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('default_score')
                    ->label('Default Score')
                    ->numeric()
                    ->required()
                    ->integer()
                    ->helperText('Positive for points earned, negative for points deducted.'),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(3),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Action')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Category'),

                Tables\Columns\TextColumn::make('default_score')
                    ->label('Score')
                    ->numeric()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => ($state >= 0 ? '+' : '') . number_format($state)),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since(),
            ])
            ->defaultSort('type')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'attendance' => 'Attendance',
                        'assignment' => 'Assignment',
                        'participation' => 'Participation',
                        'manual' => 'Manual',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScoringRules::route('/'),
            'create' => Pages\CreateScoringRule::route('/create'),
            'edit' => Pages\EditScoringRule::route('/{record}/edit'),
        ];
    }
}
