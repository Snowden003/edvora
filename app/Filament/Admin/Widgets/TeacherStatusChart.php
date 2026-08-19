<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class TeacherStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Teacher Status Distribution';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $teachers = User::where('role', 'teacher')
            ->whereHas('teacher')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = ['Active', 'Pending', 'Rejected', 'Suspended'];
        $keys   = ['active', 'pending', 'rejected', 'suspended'];
        $colors = [
            'rgba(16, 185, 129, 0.85)',
            'rgba(245, 158, 11, 0.85)',
            'rgba(239, 68, 68, 0.85)',
            'rgba(107, 114, 128, 0.85)',
        ];

        return [
            'datasets' => [
                [
                    'data'            => array_map(fn($k) => $teachers->get($k, 0), $keys),
                    'backgroundColor' => $colors,
                    'borderWidth'     => 2,
                    'borderColor'     => '#fff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
