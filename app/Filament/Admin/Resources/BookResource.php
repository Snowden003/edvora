<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BookResource\Pages;
use App\Models\Book;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Books';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $modelLabel = 'Book';

    protected static ?string $pluralModelLabel = 'Books';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'books';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Book Information')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Auto-generated from title. Must be unique.'),

                        Forms\Components\TextInput::make('author')
                            ->label('Author')
                            ->maxLength(255)
                            ->placeholder('e.g. Robert C. Martin'),

                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Category Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
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
                                    ->placeholder('e.g. book-open, academic-cap')
                                    ->helperText('Enter a Heroicon name.'),
                            ]),

                        Forms\Components\Toggle::make('is_published')
                            ->label('Published')
                            ->helperText('Only published books appear on the public books page.')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Media & File')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Cover Image')
                            ->image()
                            ->directory('books/covers')
                            ->maxSize(2048)
                            ->rules([
                                'dimensions:ratio=2/3,min_width=300,min_height=450',
                            ])
                            ->helperText('Please upload a portrait cover cropped to a 2:3 ratio (e.g. 400x600 or 600x900) before uploading. Only portrait covers are accepted. Max 2MB.'),

                        Forms\Components\FileUpload::make('pdf_file')
                            ->label('PDF File')
                            ->required()
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(51200)
                            ->directory('books/pdfs')
                            ->helperText('Upload the book PDF. Max 50MB.'),
                    ]),

                Forms\Components\Section::make('Description')
                    ->icon('heroicon-o-pencil')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Description')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->square()
                    ->defaultImageUrl(fn () => asset('assets/images/book-cover-placeholder.svg'))
                    ->size(50),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('author')
                    ->label('Author')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('download_count')
                    ->label('Downloads')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'view' => Pages\ViewBook::route('/{record}'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
