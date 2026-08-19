<?php

namespace App\Filament\Admin\Resources\BookResource\Pages;

use App\Filament\Admin\Resources\BookResource;
use Filament\Resources\Pages\EditRecord;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;
}