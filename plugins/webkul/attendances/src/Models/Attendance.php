<?php

namespace Webkul\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Attendance\Database\Factories\AttendanceFactory;
use Webkul\Attendance\Enums\AttendanceMode;
use Webkul\Attendance\Enums\OvertimeStatus;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Models\Employee;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class Attendance extends Model
{
    use BelongsToCompany;
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, SoftDeletes;

    protected $table = 'attendances_attendances';

    public function getModelTitle(): string
    {
        return __('attendances::models/attendance.title');
    }

    protected $fillable = [
        'employee_id',
        'company_id',
        'check_in',
        'check_out',
        'worked_hours',
        'expected_hours',
        'overtime_hours',
        'validated_overtime_hours',
        'overtime_status',
        'in_mode',
        'out_mode',
        'in_ip_address',
        'in_browser',
        'out_ip_address',
        'out_browser',
        'edit_reason',
        'idempotency_key',
        'creator_id',
    ];

    public function getLogAttributeLabels(): array
    {
        return [
            'employee.name'            => __('attendances::models/attendance.log-attributes.employee'),
            'check_in'                 => __('attendances::models/attendance.log-attributes.check-in'),
            'check_out'                => __('attendances::models/attendance.log-attributes.check-out'),
            'overtime_status'          => __('attendances::models/attendance.log-attributes.overtime-status'),
            'validated_overtime_hours' => __('attendances::models/attendance.log-attributes.validated-overtime-hours'),
            'edit_reason'              => __('attendances::models/attendance.log-attributes.edit-reason'),
        ];
    }

    protected $casts = [
        'check_in'                 => 'datetime',
        'check_out'                => 'datetime',
        'worked_hours'             => 'float',
        'expected_hours'           => 'float',
        'overtime_hours'           => 'float',
        'validated_overtime_hours' => 'float',
        'overtime_status'          => OvertimeStatus::class,
        'in_mode'                  => AttendanceMode::class,
        'out_mode'                 => AttendanceMode::class,
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

        static::creating(function (self $attendance) {
            $attendance->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory(): AttendanceFactory
    {
        return AttendanceFactory::new();
    }
}
