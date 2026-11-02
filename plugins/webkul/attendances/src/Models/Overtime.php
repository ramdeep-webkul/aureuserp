<?php

namespace Webkul\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Attendance\Database\Factories\OvertimeFactory;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class Overtime extends Model
{
    use BelongsToCompany;
    use HasChatter, HasFactory, HasLogActivity;

    protected $table = 'attendances_overtimes';

    public function getModelTitle(): string
    {
        return __('attendances::models/overtime.title');
    }

    protected $fillable = [
        'employee_id',
        'company_id',
        'date',
        'duration',
        'duration_real',
        'adjustment',
        'creator_id',
    ];

    public function getLogAttributeLabels(): array
    {
        return [
            'employee.name' => __('attendances::models/overtime.log-attributes.employee'),
            'date'          => __('attendances::models/overtime.log-attributes.date'),
            'duration'      => __('attendances::models/overtime.log-attributes.duration'),
            'adjustment'    => __('attendances::models/overtime.log-attributes.adjustment'),
        ];
    }

    protected $casts = [
        'date'          => 'date',
        'duration'      => 'float',
        'duration_real' => 'float',
        'adjustment'    => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $overtime) {
            $overtime->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): OvertimeFactory
    {
        return OvertimeFactory::new();
    }
}
