<?php

namespace Webkul\Attendance\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Attendance\Enums\OvertimeValidation;
use Webkul\Attendance\Settings\AttendanceSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageAttendance extends SettingsPage
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-finger-print';

    protected static string|\UnitEnum|null $navigationGroup = 'Attendance';

    protected static string $settings = AttendanceSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('attendances::filament/clusters/settings/pages/manage-attendance.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('attendances::filament/clusters/settings/pages/manage-attendance.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('attendances::filament/clusters/settings/pages/manage-attendance.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('attendances::filament/clusters/settings/pages/manage-attendance.form.sections.modes.title'))
                    ->schema([
                        Toggle::make('enable_top_bar')
                            ->label(__('attendances::filament/clusters/settings/pages/manage-attendance.form.enable-top-bar'))
                            ->helperText(__('attendances::filament/clusters/settings/pages/manage-attendance.form.enable-top-bar-helper-text')),
                        Toggle::make('auto_check_out')
                            ->label(__('attendances::filament/clusters/settings/pages/manage-attendance.form.auto-check-out'))
                            ->helperText(__('attendances::filament/clusters/settings/pages/manage-attendance.form.auto-check-out-helper-text'))
                            ->live(),
                        TextInput::make('auto_check_out_tolerance')
                            ->label(__('attendances::filament/clusters/settings/pages/manage-attendance.form.auto-check-out-tolerance'))
                            ->suffix(__('attendances::filament/clusters/settings/pages/manage-attendance.form.hours'))
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->visible(fn (Get $get): bool => (bool) $get('auto_check_out')),
                        Toggle::make('absence_management')
                            ->label(__('attendances::filament/clusters/settings/pages/manage-attendance.form.absence-management'))
                            ->helperText(__('attendances::filament/clusters/settings/pages/manage-attendance.form.absence-management-helper-text')),
                    ]),
                Section::make(__('attendances::filament/clusters/settings/pages/manage-attendance.form.sections.overtime.title'))
                    ->schema([
                        TextInput::make('overtime_company_threshold')
                            ->label(__('attendances::filament/clusters/settings/pages/manage-attendance.form.overtime-company-threshold'))
                            ->helperText(__('attendances::filament/clusters/settings/pages/manage-attendance.form.overtime-company-threshold-helper-text'))
                            ->suffix(__('attendances::filament/clusters/settings/pages/manage-attendance.form.minutes'))
                            ->integer()
                            ->minValue(0)
                            ->required(),
                        TextInput::make('overtime_employee_threshold')
                            ->label(__('attendances::filament/clusters/settings/pages/manage-attendance.form.overtime-employee-threshold'))
                            ->helperText(__('attendances::filament/clusters/settings/pages/manage-attendance.form.overtime-employee-threshold-helper-text'))
                            ->suffix(__('attendances::filament/clusters/settings/pages/manage-attendance.form.minutes'))
                            ->integer()
                            ->minValue(0)
                            ->required(),
                        Toggle::make('display_overtime')
                            ->label(__('attendances::filament/clusters/settings/pages/manage-attendance.form.display-overtime'))
                            ->helperText(__('attendances::filament/clusters/settings/pages/manage-attendance.form.display-overtime-helper-text')),
                        Select::make('overtime_validation')
                            ->label(__('attendances::filament/clusters/settings/pages/manage-attendance.form.overtime-validation'))
                            ->options(OvertimeValidation::class)
                            ->required(),
                        DatePicker::make('overtime_start_date')
                            ->label(__('attendances::filament/clusters/settings/pages/manage-attendance.form.overtime-start-date'))
                            ->helperText(__('attendances::filament/clusters/settings/pages/manage-attendance.form.overtime-start-date-helper-text'))
                            ->native(false),
                    ]),
            ]);
    }
}
