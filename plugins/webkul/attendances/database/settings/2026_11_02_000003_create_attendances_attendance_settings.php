<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('attendances_attendance.enable_top_bar', true);
        $this->migrator->add('attendances_attendance.auto_check_out', false);
        $this->migrator->add('attendances_attendance.auto_check_out_tolerance', 2.0);
        $this->migrator->add('attendances_attendance.absence_management', false);
        $this->migrator->add('attendances_attendance.overtime_company_threshold', 0);
        $this->migrator->add('attendances_attendance.overtime_employee_threshold', 0);
        $this->migrator->add('attendances_attendance.display_overtime', true);
        $this->migrator->add('attendances_attendance.overtime_validation', 'no_validation');
        $this->migrator->add('attendances_attendance.overtime_start_date', null);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('attendances_attendance.enable_top_bar');
        $this->migrator->deleteIfExists('attendances_attendance.auto_check_out');
        $this->migrator->deleteIfExists('attendances_attendance.auto_check_out_tolerance');
        $this->migrator->deleteIfExists('attendances_attendance.absence_management');
        $this->migrator->deleteIfExists('attendances_attendance.overtime_company_threshold');
        $this->migrator->deleteIfExists('attendances_attendance.overtime_employee_threshold');
        $this->migrator->deleteIfExists('attendances_attendance.display_overtime');
        $this->migrator->deleteIfExists('attendances_attendance.overtime_validation');
        $this->migrator->deleteIfExists('attendances_attendance.overtime_start_date');
    }
};
