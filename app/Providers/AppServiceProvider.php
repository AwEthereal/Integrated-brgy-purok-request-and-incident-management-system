<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use App\View\Components\Feedback\FeedbackPrompt;
use App\Providers\BroadcastServiceProvider;

/**
 * Register any application services.
 *
 * @return void
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(BroadcastServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the feedback components
        \Blade::component('feedback-prompt', FeedbackPrompt::class);
        \Blade::component('feedback-form', \App\View\Components\FeedbackForm::class);
    }
}
