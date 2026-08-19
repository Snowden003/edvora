<?php

namespace App\Filament\Admin\Resources\CourseRequestResource\Pages;

use App\Filament\Admin\Resources\CourseRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourseRequests extends ListRecords
{
    protected static string $resource = CourseRequestResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}