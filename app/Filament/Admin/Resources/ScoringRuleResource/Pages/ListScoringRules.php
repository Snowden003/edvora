<?php

namespace App\Filament\Admin\Resources\ScoringRuleResource\Pages;

use App\Filament\Admin\Resources\ScoringRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScoringRules extends ListRecords
{
    protected static string $resource = ScoringRuleResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}