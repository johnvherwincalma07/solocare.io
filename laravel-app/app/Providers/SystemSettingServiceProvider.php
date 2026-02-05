<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\SystemSetting;

class SystemSettingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $settings = cache()->remember(
                'system_settings',
                60 * 60, // 1 hour
                fn () => SystemSetting::first()
            );

            $view->with('system', $settings);

            view()->share('system', SystemSetting::first());
        });
    }
}
