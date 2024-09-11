<?php

namespace App\Providers;

use App\Events\newGavelSubmission;
use App\Listeners\SendAppropriateNotificationAfterNewGavelSubmission;

use App\Events\updatedGavelSubmission;
use App\Listeners\SendAppropriateNotificationAfterUpdatedGavelSubmission;

use App\Events\newBatchProcessRequest;
use App\Listeners\SendAppropriateNotificationAfterNewBatchProcessRequest;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        newGavelSubmission::class => [
            SendAppropriateNotificationAfterNewGavelSubmission::class,
        ],
        updatedGavelSubmission::class => [
            SendAppropriateNotificationAfterUpdatedGavelSubmission::class,
        ],
        newBatchProcessRequest::class => [
            SendAppropriateNotificationAfterNewBatchProcessRequest::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
