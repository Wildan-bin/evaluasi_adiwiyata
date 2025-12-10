<?php

namespace App\Providers;

use App\Models\FileEvidence;
use App\Policies\FileEvidencePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        FileEvidence::class => FileEvidencePolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
