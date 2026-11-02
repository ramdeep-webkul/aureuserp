<?php

namespace Webkul\Attendance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Attendance\Models\Overtime;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class OvertimePolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_attendance_overtime');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Overtime $overtime): bool
    {
        return $user->can('view_attendance_overtime');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_attendance_overtime');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Overtime $overtime): bool
    {
        if (! $overtime->adjustment || ! $user->can('update_attendance_overtime')) {
            return false;
        }

        return $this->hasAccess($user, $overtime, 'creator');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Overtime $overtime): bool
    {
        if (! $overtime->adjustment || ! $user->can('delete_attendance_overtime')) {
            return false;
        }

        return $this->hasAccess($user, $overtime, 'creator');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_attendance_overtime');
    }
}
