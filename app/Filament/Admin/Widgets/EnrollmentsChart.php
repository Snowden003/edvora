<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Enrollment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class EnrollmentsChart extends ChartWidget
{
    protected static ?string $heading = 'Course Enrollments (Last 12 Months)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }

        $enrollments = Enrollment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->keyBy(fn($row) => $row->year . '-' . str_pad($row->month, 2, '0', STR_PAD_LEFT));

        $labels = $months->map(fn($m) => $m->format('M Y'))->toArray();
        $data   = $months->map(fn($m) => $enrollments->get($m->format('Y-m'))?->total ?? 0)->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Enrollments',
                    'data'            => $data,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                    'borderColor'     => '#10b981',
                    'borderWidth'     => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
