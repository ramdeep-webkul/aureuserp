<?php

namespace Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OvertimeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label(__('attendances::filament/clusters/management/resources/overtime.form.fields.employee'))
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('date')
                    ->label(__('attendances::filament/clusters/management/resources/overtime.form.fields.date'))
                    ->required()
                    ->default(now()),
                TextInput::make('duration')
                    ->label(__('attendances::filament/clusters/management/resources/overtime.form.fields.duration'))
                    ->helperText(__('attendances::filament/clusters/management/resources/overtime.form.fields.duration-helper'))
                    ->numeric()
                    ->required(),
            ])
            ->columns(1);
    }
}
