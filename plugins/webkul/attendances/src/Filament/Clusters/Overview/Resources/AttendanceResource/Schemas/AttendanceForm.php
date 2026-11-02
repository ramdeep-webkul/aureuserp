<?php

namespace Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class AttendanceForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('employee_id')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.form.fields.employee'))
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => Auth::user()?->employee?->id),
                        DateTimePicker::make('check_in')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.form.fields.check-in'))
                            ->required()
                            ->seconds(false)
                            ->maxDate(now())
                            ->default(now()),
                        DateTimePicker::make('check_out')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.form.fields.check-out'))
                            ->seconds(false)
                            ->after('check_in'),
                        Textarea::make('edit_reason')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.form.fields.edit-reason'))
                            ->required()
                            ->visibleOn('edit')
                            ->columnSpanFull(),
                        Section::make()
                            ->visible(! empty($customFormFields))
                            ->schema($customFormFields)
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }
}
