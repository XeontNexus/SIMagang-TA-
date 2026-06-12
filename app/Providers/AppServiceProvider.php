<?php

namespace App\Providers;

use App\Models\LocationChangeRequest;
use App\Models\User;
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
        View::composer('layouts.app', function ($view) {
            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();

            $view->with('navbarBadgeCount', \App\Services\NotificationService::getNavbarBadgeCount($user));

            if ($user->isSiswa()) {
                $adminPhone = User::where('role', 'admin')
                    ->whereNotNull('no_hp')
                    ->where('no_hp', '!=', '')
                    ->orderBy('nama_lengkap')
                    ->value('no_hp');

                $view->with('adminContactPhone', $adminPhone);
            }

            if ($user->isAdmin()) {
                $view->with('pendingLocationRequests', LocationChangeRequest::pending()->count());
            }
        });

        View::composer('layouts.partials.student-sidebar', function ($view) {
            if (!Auth::check() || !Auth::user()->isSiswa()) {
                return;
            }

            $adminPhone = User::where('role', 'admin')
                ->whereNotNull('no_hp')
                ->where('no_hp', '!=', '')
                ->orderBy('nama_lengkap')
                ->value('no_hp');

            $view->with('adminContactPhone', $adminPhone);
        });
    }
}
