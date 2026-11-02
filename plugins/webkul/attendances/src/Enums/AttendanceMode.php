<?php

namespace Webkul\Attendance\Enums;

use Filament\Support\Contracts\HasLabel;

enum AttendanceMode: string implements HasLabel
{
    case MANUAL = 'manual';

    case TOP_BAR = 'top_bar';

    case TECHNICAL = 'technical';

    case AUTO_CHECK_OUT = 'auto_check_out';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MANUAL         => __('attendances::enums/attendance-mode.manual'),
            self::TOP_BAR        => __('attendances::enums/attendance-mode.top-bar'),
            self::TECHNICAL      => __('attendances::enums/attendance-mode.technical'),
            self::AUTO_CHECK_OUT => __('attendances::enums/attendance-mode.auto-check-out'),
        };
    }

    public static function options(): array
    {
        return [
            self::MANUAL->value         => __('attendances::enums/attendance-mode.manual'),
            self::TOP_BAR->value        => __('attendances::enums/attendance-mode.top-bar'),
            self::TECHNICAL->value      => __('attendances::enums/attendance-mode.technical'),
            self::AUTO_CHECK_OUT->value => __('attendances::enums/attendance-mode.auto-check-out'),
        ];
    }
}
