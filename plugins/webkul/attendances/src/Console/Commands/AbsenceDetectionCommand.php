<?php

namespace Webkul\Attendance\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

class AbsenceDetectionCommand extends Command
{
    protected $signature = 'attendances:detect-absences';

    protected $description = 'Record yesterday\'s absences for scheduled employees who did not check in.';

    public function handle(): int
    {
        if (! Package::isPluginInstalled('attendances')) {
            return self::SUCCESS;
        }

        $count = 0;

        foreach ([...Company::all(), null] as $company) {
            $count += AttendanceWorkflow::forCompany(
                $company,
                fn (AttendanceWorkflow $workflow) => $workflow->detectAbsences($company)->count()
            );
        }

        $this->info(__('attendances::system.commands.detect-absences', ['count' => $count]));

        return self::SUCCESS;
    }
}
