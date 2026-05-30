<?php

namespace App\Providers;

use App\Models\LocationChangeRequest;
use App\Models\StudentNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Only load view composer if user is authenticated
        if (Auth::check()) {
            View::composer('layouts.app', function ($view) {
                if (Auth::user()->isSiswa()) {
                    $view->with('studentNotifications', StudentNotification::where('user_id', Auth::id())
                        ->whereNull('read_at')
                        ->latest()
                        ->limit(5)
                        ->get());
                }

                if (Auth::user()->isAdmin()) {
                    $view->with('pendingLocationRequests', LocationChangeRequest::pending()->count());
                }
            });
        }
    }
}
