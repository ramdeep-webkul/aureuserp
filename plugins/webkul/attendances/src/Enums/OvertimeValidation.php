<?php

namespace Webkul\Attendance\Enums;

use Filament\Support\Contracts\HasLabel;

enum OvertimeValidation: string implements HasLabel
{
    case NO_VALIDATION = 'no_validation';

    case BY_MANAGER = 'by_manager';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NO_VALIDATION => __('attendances::enums/overtime-validation.no-validation'),
            self::BY_MANAGER    => __('attendances::enums/overtime-validation.by-manager'),
        };
    }

    public static function options(): array
    {
        return [
            self::NO_VALIDATION->value => __('attendances::enums/overtime-validation.no-validation'),
            self::BY_MANAGER->value    => __('attendances::enums/overtime-validation.by-manager'),
        ];
    }
}
