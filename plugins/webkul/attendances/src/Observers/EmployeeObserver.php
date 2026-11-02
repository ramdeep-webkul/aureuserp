<?php

namespace Webkul\Attendance\Observers;

use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Employee\Models\Employee;
use Webkul\PluginManager\Package;

class EmployeeObserver
{
    public function deleted(Employee $employee): void
    {
        if (! Package::isPluginInstalled('attendances') || $employee->isForceDeleting()) {
            return;
        }

        app(AttendanceWorkflow::class)->closeForArchivedEmployee($employee);
    }
}
