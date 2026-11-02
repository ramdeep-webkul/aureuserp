<?php

namespace Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource;

class AttendanceInfolist
{
    public static function configure(Schema $schema, array $customInfolistEntries = []): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('employee.name')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.employee'))
                            ->icon('heroicon-o-user'),
                        TextEntry::make('check_in')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.check-in'))
                            ->dateTime()
                            ->icon('heroicon-o-arrow-right-end-on-rectangle'),
                        TextEntry::make('check_out')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.check-out'))
                            ->dateTime()
                            ->placeholder('—')
                            ->icon('heroicon-o-arrow-left-start-on-rectangle'),
                        TextEntry::make('worked_hours')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.worked-hours'))
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('expected_hours')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.expected-hours'))
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('edit_reason')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.edit-reason'))
                            ->placeholder('—')
                            ->columnSpanFull(),
                        ...$customInfolistEntries,
                    ])
                    ->columns(3),
                Section::make(__('attendances::filament/clusters/overview/resources/attendance.infolist.sections.overtime'))
                    ->schema([
                        TextEntry::make('overtime_hours')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.overtime-hours'))
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state)),
                        TextEntry::make('validated_overtime_hours')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.validated-overtime-hours'))
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state)),
                        TextEntry::make('overtime_status')
                            ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.overtime-status'))
                            ->badge(),
                    ])
                    ->columns(3),
                Section::make(__('attendances::filament/clusters/overview/resources/attendance.infolist.sections.details'))
                    ->schema([
                        Fieldset::make(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.check-in'))
                            ->schema([
                                TextEntry::make('in_mode')
                                    ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.mode'))
                                    ->placeholder('—'),
                                TextEntry::make('in_ip_address')
                                    ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.ip-address'))
                                    ->placeholder('—'),
                                TextEntry::make('in_browser')
                                    ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.browser'))
                                    ->placeholder('—'),
                            ])
                            ->columns(1),
                        Fieldset::make(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.check-out'))
                            ->schema([
                                TextEntry::make('out_mode')
                                    ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.mode'))
                                    ->placeholder('—'),
                                TextEntry::make('out_ip_address')
                                    ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.ip-address'))
                                    ->placeholder('—'),
                                TextEntry::make('out_browser')
                                    ->label(__('attendances::filament/clusters/overview/resources/attendance.infolist.entries.browser'))
                                    ->placeholder('—'),
                            ])
                            ->columns(1),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ])
            ->columns(1);
    }
}
