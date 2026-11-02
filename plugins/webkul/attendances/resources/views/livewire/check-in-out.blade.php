<div>
    @if ($this->isVisible())
        @php
            $checkedIn = (bool) $this->openAttendance;
            $overtime = $this->overtimeBalance();
        @endphp

        <x-filament::dropdown placement="bottom-end" width="xs" teleport>
            <x-slot name="trigger">
                <x-filament::icon-button
                    icon="heroicon-o-clock"
                    :color="$checkedIn ? 'success' : 'danger'"
                    :label="$checkedIn
                        ? __('attendances::livewire/check-in-out.status.checked-in')
                        : __('attendances::livewire/check-in-out.status.checked-out')"
                />
            </x-slot>

            <div style="display:flex;flex-direction:column;gap:0.75rem;padding:1rem;">
                <div style="display:flex;flex-direction:column;gap:0.25rem;">
                    <span style="font-weight:600;">{{ $this->employee->name }}</span>

                    <x-filament::badge :color="$checkedIn ? 'success' : 'danger'" style="align-self:flex-start;">
                        {{ $checkedIn
                            ? __('attendances::livewire/check-in-out.status.checked-in')
                            : __('attendances::livewire/check-in-out.status.checked-out') }}
                    </x-filament::badge>
                </div>

                <div style="display:flex;justify-content:space-between;font-size:0.875rem;">
                    <span>{{ __('attendances::livewire/check-in-out.hours-today') }}</span>

                    <span style="font-weight:600;">{{ \Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource::formatHours($this->hoursToday()) }}</span>
                </div>

                @if ($overtime !== null)
                    <div style="display:flex;justify-content:space-between;font-size:0.875rem;">
                        <span>{{ __('attendances::livewire/check-in-out.overtime-balance') }}</span>

                        <span style="font-weight:600;">{{ \Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource::formatHours($overtime) }}</span>
                    </div>
                @endif

                <x-filament::button
                    wire:click="toggle"
                    :color="$checkedIn ? 'danger' : 'success'"
                    :icon="$checkedIn ? 'heroicon-o-arrow-right-start-on-rectangle' : 'heroicon-o-arrow-left-end-on-rectangle'"
                    style="width:100%;justify-content:center;"
                >
                    {{ $checkedIn
                        ? __('attendances::livewire/check-in-out.actions.check-out')
                        : __('attendances::livewire/check-in-out.actions.check-in') }}
                </x-filament::button>
            </div>
        </x-filament::dropdown>
    @endif
</div>
