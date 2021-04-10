<?php

namespace App\Providers;

use App\Asset;
use App\Notification;
use App\Observers\AssetObserver;
use App\Observers\TeamObserver;
use App\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ADDED NOTIFICATION BELL COUNTER
        view()->composer('*', function ($view) {
            $user_id = auth()->user()->id;
            $count_notification = Notification::where('user_id', $user_id)
                ->where('read', 0)
                ->count();
            View::share('count_notification', $count_notification);
            // END NOTIFICATION
        });

        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->roles()->where('role_id', 1)->first() != null;
        });

        Blade::if('user', function () {
            return auth()->check() && auth()->user()->roles()->where('role_id', 2)->first() != null;
        });

        Asset::observe(AssetObserver::class);
        Team::observe(TeamObserver::class);
    }
}
