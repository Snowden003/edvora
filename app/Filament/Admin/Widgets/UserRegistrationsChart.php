<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UserRegistrationsChart extends ChartWidget
{
    protected static ?string $heading = 'User Registrations (Last 12 Months)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [
            'all'     => 'All Users',
            'student' => 'Students Only',
            'teacher' => 'Teachers Only',
        ];
    }

    protected function getData(): array
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }

        $query = User::query();
        if ($this->filter && $this->filter !== 'all') {
            $query->where('role', $this->filter);
        }

        $registrations = $query
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->keyBy(fn($row) => $row->year . '-' . str_pad($row->month, 2, '0', STR_PAD_LEFT));

        $labels = $months->map(fn($m) => $m->format('M Y'))->toArray();
        $data   = $months->map(fn($m) => $registrations->get($m->format('Y-m'))?->total ?? 0)->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Registrations',
                    'data'            => $data,
                    'borderColor'     => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
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
