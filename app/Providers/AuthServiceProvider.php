<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\Request;
use App\Policies\RequestPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Request::class => RequestPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Set the URL for email verification links
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Customize the email verification URL
        VerifyEmail::createUrlUsing(function ($notifiable) {
            return URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });
    }
}
