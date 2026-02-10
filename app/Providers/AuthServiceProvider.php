<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\Request;
use App\Models\IncidentReport;
use App\Models\ResidentRecord;
use App\Policies\RequestPolicy;
use App\Policies\IncidentReportPolicy;
use App\Policies\ResidentRecordPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Request::class => RequestPolicy::class,
        IncidentReport::class => IncidentReportPolicy::class,
        ResidentRecord::class => ResidentRecordPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Gate for Barangay Official actions
        Gate::define('barangay-official-actions', function ($user) {
            return in_array($user->role, [
                'barangay_captain',
                'barangay_kagawad',
                'secretary',
                'sk_chairman',
                'admin',
            ]);
        });

        // Set the URL scheme for email verification links
        // Only force https when the app is actually configured to run on https.
        $appUrl = (string) config('app.url');
        if ($appUrl !== '' && str_starts_with($appUrl, 'https://')) {
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
