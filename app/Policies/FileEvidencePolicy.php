<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FileEvidence;

class FileEvidencePolicy
{
    /**
     * Determine if user can view file evidence
     */
    public function view(User $user, FileEvidence $fileEvidence): bool
    {
        // User dapat view jika mereka yang submit
        if ($fileEvidence->submission->user_id === $user->id) {
            return true;
        }

        // Admin dapat view semua
        if ($user->is_admin) {
            return true;
        }

        return false;
    }

    /**
     * Determine if user can download file
     */
    public function download(User $user, FileEvidence $fileEvidence): bool
    {
        return $this->view($user, $fileEvidence);
    }
}
