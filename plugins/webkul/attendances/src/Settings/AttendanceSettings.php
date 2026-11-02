<?php

namespace Webkul\Attendance\Settings;

use Spatie\LaravelSettings\Settings;
use Webkul\Attendance\Enums\OvertimeValidation;

class AttendanceSettings extends Settings
{
    public bool $enable_top_bar;

    public bool $auto_check_out;

    public float $auto_check_out_tolerance;

    public bool $absence_management;

    public int $overtime_company_threshold;

    public int $overtime_employee_threshold;

    public bool $display_overtime;

    public OvertimeValidation $overtime_validation;

    public ?string $overtime_start_date;

    public static function cacheKey(): string
    {
        return static::class.':'.(current_company_id() ?? 0);
    }

    public static function group(): string
    {
        return 'attendances_attendance';
    }
}
