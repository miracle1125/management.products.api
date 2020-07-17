<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Observers\OrderObserver;
use App\Observers\OrderProductObserver;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if(empty(env('TENANT_NAME'))) {
            dd('TENANT_NAME not specified');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::before(function (JobProcessing $event) {
            logger('Job starting '.$event->job->resolveName());
        });

        Queue::after(function (JobProcessed $event) {
            logger('Job processed '.$event->job->resolveName());
        });

        Order::observe(OrderObserver::class);
        OrderProduct::observe(OrderProductObserver::class);

    }
}
