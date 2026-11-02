<?php

namespace Webkul\Attendance\Exceptions;

use Exception;

class AttendanceValidationException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
