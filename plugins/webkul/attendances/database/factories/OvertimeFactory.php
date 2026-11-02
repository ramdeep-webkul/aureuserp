<?php

namespace Webkul\Attendance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Attendance\Models\Overtime;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Overtime>
 */
class OvertimeFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Overtime::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id'   => Employee::factory(),
            'date'          => now()->subDay()->toDateString(),
            'duration'      => 1.0,
            'duration_real' => 1.0,
            'adjustment'    => false,
        ];
    }
}
