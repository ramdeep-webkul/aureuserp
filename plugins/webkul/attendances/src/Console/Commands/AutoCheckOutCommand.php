<?php

namespace Webkul\Attendance\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

class AutoCheckOutCommand extends Command
{
    protected $signature = 'attendances:auto-check-out';

    protected $description = 'Check out employees who stayed checked in past their working hours and the allowed tolerance.';

    public function handle(): int
    {
        if (! Package::isPluginInstalled('attendances')) {
            return self::SUCCESS;
        }

        $count = 0;

        foreach ([...Company::all(), null] as $company) {
            $count += AttendanceWorkflow::forCompany(
                $company,
                fn (AttendanceWorkflow $workflow) => $workflow->autoCheckOut($company)->count()
            );
        }

        $this->info(__('attendances::system.commands.auto-check-out', ['count' => $count]));

        return self::SUCCESS;
    }
}
