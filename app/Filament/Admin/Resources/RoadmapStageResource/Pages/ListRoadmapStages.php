<?php

namespace App\Filament\Admin\Resources\RoadmapStageResource\Pages;

use App\Filament\Admin\Resources\RoadmapStageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoadmapStages extends ListRecords
{
    protected static string $resource = RoadmapStageResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}