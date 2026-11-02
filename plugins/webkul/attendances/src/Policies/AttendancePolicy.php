<?php

namespace Webkul\Attendance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class AttendancePolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_attendance_attendance');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        return $user->can('view_attendance_attendance');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_attendance_attendance');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        if (! $user->can('update_attendance_attendance')) {
            return false;
        }

        return $this->hasAccess($user, $attendance, 'creator')
            || $this->isOwnerOrManager($user, $attendance);
    }

    /**
     * Determine whether the user can approve or refuse the overtime.
     */
    public function approve(User $user, Attendance $attendance): bool
    {
        if (! $user->can('update_attendance_attendance')) {
            return false;
        }

        return app(AttendanceWorkflow::class)->canApprove($user, $attendance);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        if (! $user->can('delete_attendance_attendance')) {
            return false;
        }

        return $this->hasAccess($user, $attendance, 'creator')
            || $this->isOwnerOrManager($user, $attendance);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_attendance_attendance');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Attendance $attendance): bool
    {
        if (! $user->can('restore_attendance_attendance')) {
            return false;
        }

        return $this->hasAccess($user, $attendance, 'creator')
            || $this->isOwnerOrManager($user, $attendance);
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_attendance_attendance');
    }

    protected function isOwnerOrManager(User $user, Attendance $attendance): bool
    {
        return in_array($user->id, [
            $attendance->employee?->user_id,
            $attendance->employee?->attendance_manager_id,
        ], true);
    }
}
