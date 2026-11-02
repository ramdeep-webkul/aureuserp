<?php

namespace Webkul\Attendance\Exceptions;

use Exception;

class AttendanceAccessException extends Exception
{
    public function getStatusCode(): int
    {
        return 403;
    }
}
