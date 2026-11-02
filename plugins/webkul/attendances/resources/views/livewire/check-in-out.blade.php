<div>
    @if ($this->employee)
        @php
            $checkedIn = (bool) $this->openAttendance;
            $overtime = $this->overtimeBalance();
            $timezone = app(\Webkul\Attendance\Services\WorkedHoursCalculator::class)->timezone($this->employee);
        @endphp

        <style>
            .fi-topbar .fi-user-menu-trigger {
                position: relative;
            }

            .fi-topbar .fi-user-menu-trigger::after {
                content: '';
                position: absolute;
                right: -0.125rem;
                bottom: -0.125rem;
                width: 0.625rem;
                height: 0.625rem;
                border-radius: 9999px;
                background-color: {{ $checkedIn ? 'var(--success-500)' : 'var(--danger-500)' }};
                box-shadow: 0 0 0 2px var(--color-white);
            }

            .dark .fi-topbar .fi-user-menu-trigger::after {
                box-shadow: 0 0 0 2px var(--gray-900);
            }
        </style>

        <x-filament::modal :id="\Webkul\Attendance\Livewire\CheckInOut::MODAL_ID" width="md">
            <x-slot name="heading">
                {{ __('attendances::livewire/check-in-out.heading') }}
            </x-slot>

            <div class="flex flex-col items-center gap-2 text-center">
                <span class="text-lg font-semibold text-gray-950 dark:text-white">
                    {{ $this->employee->name }}
                </span>

                <x-filament::badge :color="$checkedIn ? 'success' : 'danger'">
                    {{ $checkedIn
                        ? __('attendances::livewire/check-in-out.status.checked-in')
                        : __('attendances::livewire/check-in-out.status.checked-out') }}
                </x-filament::badge>

                @if ($checkedIn)
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('attendances::livewire/check-in-out.since', ['time' => $this->openAttendance->check_in->copy()->setTimezone($timezone)->format('H:i')]) }}
                    </span>
                @endif
            </div>

            <div class="grid gap-2 {{ $overtime !== null ? 'grid-cols-2' : 'grid-cols-1' }}">
                <div class="flex flex-col items-center gap-1 rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('attendances::livewire/check-in-out.hours-today') }}</span>

                    <span class="text-xl font-semibold text-gray-950 dark:text-white">{{ \Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource::formatHours($this->hoursToday()) }}</span>
                </div>

                @if ($overtime !== null)
                    <div class="flex flex-col items-center gap-1 rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('attendances::livewire/check-in-out.overtime-balance') }}</span>

                        <span class="text-xl font-semibold text-gray-950 dark:text-white">{{ \Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource::formatHours($overtime) }}</span>
                    </div>
                @endif
            </div>

            <x-filament::button
                size="lg"
                wire:click="toggle"
                wire:loading.attr="disabled"
                :color="$checkedIn ? 'danger' : 'success'"
                :icon="$checkedIn ? 'heroicon-o-arrow-right-start-on-rectangle' : 'heroicon-o-arrow-left-end-on-rectangle'"
            >
                {{ $checkedIn
                    ? __('attendances::livewire/check-in-out.actions.check-out')
                    : __('attendances::livewire/check-in-out.actions.check-in') }}
            </x-filament::button>
        </x-filament::modal>
    @endif
</div>
