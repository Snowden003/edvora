<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Course;
use Filament\Widgets\ChartWidget;

class CoursesByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Courses by Category';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $courses = Course::selectRaw('categories.name as category_name, COUNT(courses.id) as total')
            ->join('categories', 'categories.id', '=', 'courses.category_id')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $palette = [
            'rgba(59,  130, 246, 0.85)',
            'rgba(16,  185, 129, 0.85)',
            'rgba(245, 158, 11,  0.85)',
            'rgba(239, 68,  68,  0.85)',
            'rgba(139, 92,  246, 0.85)',
            'rgba(236, 72,  153, 0.85)',
            'rgba(20,  184, 166, 0.85)',
            'rgba(249, 115, 22,  0.85)',
        ];

        return [
            'datasets' => [
                [
                    'data'            => $courses->pluck('total')->toArray(),
                    'backgroundColor' => array_slice($palette, 0, $courses->count()),
                    'borderWidth'     => 2,
                    'borderColor'     => '#fff',
                ],
            ],
            'labels' => $courses->pluck('category_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
