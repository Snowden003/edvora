<?php

namespace App\Filament\Admin\Widgets;

use App\Models\ClassSession;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ClassSessionsChart extends ChartWidget
{
    protected static ?string $heading = 'Live Class Sessions (Last 30 Days)';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $days->push(Carbon::now()->subDays($i)->startOfDay());
        }

        $sessions = ClassSession::selectRaw('DATE(started_at) as day, COUNT(*) as total')
            ->where('started_at', '>=', Carbon::now()->subDays(30)->startOfDay())
            ->groupByRaw('DATE(started_at)')
            ->get()
            ->keyBy('day');

        $labels = $days->map(fn($d) => $d->format('M d'))->toArray();
        $data   = $days->map(fn($d) => $sessions->get($d->toDateString())?->total ?? 0)->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Sessions',
                    'data'            => $data,
                    'borderColor'     => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.15)',
                    'fill'            => true,
                    'tension'         => 0.3,
                    'pointRadius'     => 3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
