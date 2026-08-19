<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Certificates';
    protected static ?string $navigationGroup = 'Student Management';
    protected static ?string $modelLabel = 'Certificate';
    protected static ?string $pluralModelLabel = 'Certificates';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Certificate Information')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Student')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(fn() => User::where('role', 'student')->pluck('name', 'id')->toArray()),

                        Forms\Components\Select::make('course_id')
                            ->label('Course')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(fn() => Course::pluck('title', 'id')->toArray()),

                        Forms\Components\TextInput::make('title')
                            ->label('Certificate Title')
                            ->required()
                            ->maxLength(255)
                            ->default('Certificate of Completion'),

                        Forms\Components\TextInput::make('certificate_number')
                            ->label('Certificate Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn() => 'CERT-' . strtoupper(Str::random(8))),

                        Forms\Components\DateTimePicker::make('issued_at')
                            ->label('Issue Date')
                            ->required()
                            ->default(now()),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Certificate Files')
                    ->icon('heroicon-o-folder')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Certificate PDF')
                            ->directory('certificates/pdfs')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120),

                        Forms\Components\FileUpload::make('image_path')
                            ->label('Certificate Image')
                            ->image()
                            ->directory('certificates/images')
                            ->maxSize(5120)
                            ->helperText('Upload the certificate template image with user name on it'),
                    ]),
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

                Tables\Columns\TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('certificate_number')
                    ->label('Certificate #')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Issued At')
                    ->dateTime('M d, Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('has_image')
                    ->label('Image')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn($record) => !empty($record->image_path)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('issued_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Course')
                    ->options(fn() => Course::pluck('title', 'id')->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('generate')
                    ->label('Generate')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->url(fn($record) => static::getUrl('generate', ['record' => $record])),
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
            'index' => Pages\ListCertificates::route('/'),
            'create' => Pages\CreateCertificate::route('/create'),
            'edit' => Pages\EditCertificate::route('/{record}/edit'),
            'generate' => Pages\GenerateCertificate::route('/{record}/generate'),
        ];
    }
}
