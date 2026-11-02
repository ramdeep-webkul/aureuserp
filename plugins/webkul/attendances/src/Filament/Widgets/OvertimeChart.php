<?php

namespace Webkul\Attendance\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Attendance\Models\Overtime;

class OvertimeChart extends ChartWidget
{
    use HasWidgetShield;

    protected const MONTHS = 12;

    protected const EXTRA_COLOR = '#16a34a';

    protected const SHORTFALL_COLOR = '#dc2626';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('attendances::filament/widgets/overtime-chart.heading');
    }

    protected function getData(): array
    {
        $start = now()->startOfMonth()->subMonths(static::MONTHS - 1);

        $query = Overtime::query();

        $bucket = db_dialect()->monthBucket($query->qualifyColumn('date'));

        $duration = $query->qualifyColumn('duration');

        $totals = $query
            ->where($query->qualifyColumn('date'), '>=', $start->toDateString())
            ->selectRaw("{$bucket} as period, SUM(CASE WHEN {$duration} > 0 THEN {$duration} ELSE 0 END) as extra, SUM(CASE WHEN {$duration} < 0 THEN {$duration} ELSE 0 END) as shortfall")
            ->groupByRaw($bucket)
            ->get()
            ->keyBy('period');

        $labels = [];
        $extra = [];
        $shortfall = [];

        for ($month = 0; $month < static::MONTHS; $month++) {
            $cursor = $start->copy()->addMonths($month);

            $labels[] = $cursor->translatedFormat('M Y');
            $extra[] = round((float) ($totals[$cursor->format('Y-m')]->extra ?? 0), 2);
            $shortfall[] = round((float) ($totals[$cursor->format('Y-m')]->shortfall ?? 0), 2);
        }

        return [
            'datasets' => [
                [
                    'label'           => __('attendances::filament/widgets/overtime-chart.extra'),
                    'data'            => $extra,
                    'backgroundColor' => static::EXTRA_COLOR,
                    'borderColor'     => static::EXTRA_COLOR,
                    'maxBarThickness' => 28,
                ],
                [
                    'label'           => __('attendances::filament/widgets/overtime-chart.shortfall'),
                    'data'            => $shortfall,
                    'backgroundColor' => static::SHORTFALL_COLOR,
                    'borderColor'     => static::SHORTFALL_COLOR,
                    'maxBarThickness' => 28,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'scales'              => [
                'x' => ['stacked' => true, 'grid' => ['display' => false]],
                'y' => ['stacked' => true],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
