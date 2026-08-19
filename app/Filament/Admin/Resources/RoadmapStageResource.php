<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoadmapStageResource\Pages;
use App\Models\RoadmapStage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoadmapStageResource extends Resource
{
    protected static ?string $model = RoadmapStage::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Roadmap Stages';
    protected static ?string $navigationGroup = 'Website Management';
    protected static ?string $modelLabel = 'Roadmap Stage';
    protected static ?string $pluralModelLabel = 'Roadmap Stages';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('stage_number')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Stage 1')
                    ->label('Stage Number'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Title'),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->rows(4)
                    ->label('Description'),
                Forms\Components\FileUpload::make('image_url')
                    ->image()
                    ->disk('public')
                    ->directory('roadmap-stages')
                    ->maxSize(5120)
                    ->label('Stage Image'),
                Forms\Components\TextInput::make('duration')
                    ->maxLength(255)
                    ->placeholder('2-3 months')
                    ->label('Duration'),
                Forms\Components\TagsInput::make('skills')
                    ->placeholder('Add a skill')
                    ->label('Skills')
                    ->helperText('Enter skill names.'),
                Forms\Components\TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Display Order'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stage_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('duration')
                    ->placeholder('-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoadmapStages::route('/'),
            'create' => Pages\CreateRoadmapStage::route('/create'),
            'edit' => Pages\EditRoadmapStage::route('/{record}/edit'),
        ];
    }
}
