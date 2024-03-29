<?php

namespace App\Modules\AutoStatusPackingWeb\src;

use App\Events\Order\OrderUpdatedEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\SetPackingWebStatus::class,
        ],
    ];
}
