<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\SoloParentApplication;
use App\Models\ScheduledSubmission;
use App\Models\HomeVisit;
use App\Models\SystemSetting;


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
        // View Composer to share notifications with all views
        View::composer('*', function ($view) {
            $scheduledNotifications = [];

            if (Auth::check()) {
                $user = Auth::user();

                // Get latest submitted application
                $application = SoloParentApplication::where('user_id', $user->id)
                    ->where('is_submitted', true)
                    ->latest()
                    ->first();

                if ($application) {
                    // Scheduled submissions
                    $scheduledSubmissions = ScheduledSubmission::where('application_id', $application->id)
                        ->where('scheduled_date', '>=', now())
                        ->orderBy('scheduled_date', 'asc')
                        ->get();

                    foreach ($scheduledSubmissions as $sched) {
                        $scheduledNotifications[] = [
                            'type' => 'Scheduled Submission',
                            'date' => $sched->scheduled_date,
                            'message' => 'Your application is scheduled for submission on ' . $sched->scheduled_date->format('F d, Y')
                        ];
                    }

                    // Home visits
                    $homeVisits = HomeVisit::where('application_id', $application->id)
                        ->where('visit_date', '>=', now())
                        ->orderBy('visit_date', 'asc')
                        ->get();

                    foreach ($homeVisits as $visit) {
                        $scheduledNotifications[] = [
                            'type' => 'Home Visit',
                            'date' => $visit->visit_date,
                            'message' => 'Your home visit is scheduled on ' . $visit->visit_date->format('F d, Y')
                        ];
                    }

                    // Sort notifications by date
                    usort($scheduledNotifications, function ($a, $b) {
                        return strtotime($a['date']) - strtotime($b['date']);
                    });
                }
            }

            $view->with('scheduledNotifications', $scheduledNotifications);
        });

        view()->share('system', SystemSetting::first());
    }
    
}
