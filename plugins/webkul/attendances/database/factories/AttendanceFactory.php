<?php

namespace Webkul\Attendance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Attendance\Enums\AttendanceMode;
use Webkul\Attendance\Models\Attendance;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = now()->subDay()->setTime(9, 0);

        return [
            'employee_id' => Employee::factory(),
            'check_in'    => $checkIn,
            'check_out'   => $checkIn->copy()->addHours(8),
            'in_mode'     => AttendanceMode::MANUAL,
            'out_mode'    => AttendanceMode::MANUAL,
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'check_out' => null,
            'out_mode'  => null,
        ]);
    }
}
