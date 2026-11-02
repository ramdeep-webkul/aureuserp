<?php

namespace Webkul\Attendance\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Attendance\Models\Attendance;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\Scopes\CompanyScope;

class WorkedHoursChart extends ChartWidget
{
    use HasWidgetShield;

    protected const WEEKS = 8;

    protected const TOP_EMPLOYEES = 8;

    protected const COLORS = ['#2a78d6', '#16a34a', '#d97706', '#9333ea', '#dc2626', '#0891b2', '#db2777', '#65a30d', '#898781'];

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('attendances::filament/widgets/worked-hours-chart.heading');
    }

    protected function getData(): array
    {
        $start = now()->startOfWeek()->subWeeks(static::WEEKS - 1);

        $query = Attendance::query();

        $day = 'DATE('.$query->qualifyColumn('check_in').')';

        $rows = $query
            ->whereNotNull($query->qualifyColumn('check_out'))
            ->where($query->qualifyColumn('check_in'), '>=', $start)
            ->selectRaw($query->qualifyColumn('employee_id')." as employee_id, {$day} as day, SUM(".$query->qualifyColumn('worked_hours').') as hours')
            ->groupBy($query->qualifyColumn('employee_id'))
            ->groupByRaw($day)
            ->get();

        $weeks = [];

        for ($week = 0; $week < static::WEEKS; $week++) {
            $weeks[] = $start->copy()->addWeeks($week)->format('Y-m-d');
        }

        $hours = [];

        foreach ($rows as $row) {
            $week = Carbon::parse($row->day)->startOfWeek()->format('Y-m-d');

            $hours[$row->employee_id][$week] = ($hours[$row->employee_id][$week] ?? 0) + (float) $row->hours;
        }

        $totals = collect($hours)->map(fn (array $weekHours) => array_sum($weekHours))->sortDesc();

        $topIds = $totals->keys()->take(static::TOP_EMPLOYEES)->all();

        $names = Employee::withTrashed()
            ->withoutGlobalScope(CompanyScope::class)
            ->whereIn('id', $topIds)
            ->pluck('name', 'id');

        $datasets = [];

        foreach ($topIds as $index => $employeeId) {
            $datasets[] = $this->dataset($names[$employeeId] ?? '—', $weeks, $hours[$employeeId], static::COLORS[$index]);
        }

        $others = [];

        foreach ($totals->keys()->slice(static::TOP_EMPLOYEES) as $employeeId) {
            foreach ($hours[$employeeId] as $week => $value) {
                $others[$week] = ($others[$week] ?? 0) + $value;
            }
        }

        if ($others) {
            $datasets[] = $this->dataset(__('attendances::filament/widgets/worked-hours-chart.others'), $weeks, $others, static::COLORS[static::TOP_EMPLOYEES]);
        }

        return [
            'datasets' => $datasets,
            'labels'   => array_map(fn (string $week) => Carbon::parse($week)->translatedFormat('d M'), $weeks),
        ];
    }

    protected function dataset(string $label, array $weeks, array $hours, string $color): array
    {
        return [
            'label'           => $label,
            'data'            => array_map(fn (string $week) => round($hours[$week] ?? 0, 2), $weeks),
            'backgroundColor' => $color,
            'borderColor'     => $color,
            'maxBarThickness' => 28,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'scales'              => [
                'x' => ['stacked' => true, 'grid' => ['display' => false]],
                'y' => ['stacked' => true, 'beginAtZero' => true],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
