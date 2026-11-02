<?php

namespace Webkul\Attendance\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OvertimeStatus: string implements HasColor, HasLabel
{
    case TO_APPROVE = 'to_approve';

    case APPROVED = 'approved';

    case REFUSED = 'refused';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TO_APPROVE => __('attendances::enums/overtime-status.to-approve'),
            self::APPROVED   => __('attendances::enums/overtime-status.approved'),
            self::REFUSED    => __('attendances::enums/overtime-status.refused'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::TO_APPROVE => 'warning',
            self::APPROVED   => 'success',
            self::REFUSED    => 'danger',
        };
    }

    public static function options(): array
    {
        return [
            self::TO_APPROVE->value => __('attendances::enums/overtime-status.to-approve'),
            self::APPROVED->value   => __('attendances::enums/overtime-status.approved'),
            self::REFUSED->value    => __('attendances::enums/overtime-status.refused'),
        ];
    }
}
