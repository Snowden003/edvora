<?php

namespace App\Filament\Admin\Resources\CourseResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Course Students / شاگردان دوره';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->label('Enrollment Status / وضعیت شمولیت')
                    ->options([
                        'active' => 'Active / فعال',
                        'completed' => 'Completed / فارغ‌التحصیل',
                        'dropped' => 'Dropped / انصراف',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('progress_percentage')
                    ->label('Progress (%) / درصد پیشرفت')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('studentProfile'))
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=1F8FFF&color=fff')
                    ->size(36),

                Tables\Columns\TextColumn::make('name')
                    ->label('Full Name / نام کامل')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('studentProfile.father_name')
                    ->label('Father Name / ولد')
                    ->placeholder('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('studentProfile.national_id')
                    ->label('National ID / تذکره')
                    ->placeholder('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone / تماس')
                    ->placeholder('—')
                    ->getStateUsing(fn ($record) => $record->phone ?? $record->studentProfile?->phone_number),

                Tables\Columns\TextColumn::make('studentProfile.province')
                    ->label('Province / ولایت')
                    ->placeholder('—'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status / وضعیت')
                    ->colors([
                        'success' => 'active',
                        'info' => 'completed',
                        'danger' => 'dropped',
                    ]),

                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progress / پیشرفت')
                    ->formatStateUsing(fn ($state) => ($state ?? 0) . '%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enrolled At / تاریخ ثبت‌نام')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'dropped' => 'Dropped',
                    ]),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تغییر وضعیت')
                    ->modalHeading('Edit Enrollment Status'),
                Tables\Actions\DetachAction::make()
                    ->label('حذف از دوره')
                    ->modalHeading('Remove student from course'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
