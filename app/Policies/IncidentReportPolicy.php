<?php

namespace App\Policies;

use App\Models\User;
use App\Models\IncidentReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class IncidentReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can approve the incident report.
     */
    public function approve(User $user): bool
    {
        return $this->isBarangayOfficial($user);
    }

    /**
     * Determine whether the user can reject the incident report.
     */
    public function reject(User $user): bool
    {
        return $this->isBarangayOfficial($user);
    }

    /**
     * Determine whether the user can update the incident report status.
     */
    public function update(User $user, IncidentReport $incidentReport): bool
    {
        return $this->isBarangayOfficial($user);
    }

    /**
     * Check if the user is a barangay official (any role except purok_leader)
     */
    protected function isBarangayOfficial(User $user): bool
    {
        return in_array($user->role, [
            'barangay_captain',
            'barangay_kagawad',
            'secretary',
            'barangay_clerk',
            'sk_chairman',
            'admin'
        ]);
    }
}
