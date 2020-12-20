<?php

namespace App\Observers;

use App\Events\Order\StatusChangedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Services\OrderService;

class OrderObserver
{
    /**
     * Handle the order "created" event.
     *
     * @param Order $order
     * @return void
     */
    public function created(Order $order)
    {
        // we will not dispatch CreatedEvent here
        // please user OrderService method
        // CreatedEvent should be dispatched
        // after created OrderProducts etc
    }

    /**
     * Handle the order "updated" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        OrderUpdatedEvent::dispatch($order);

        if ($order['status_code'] !== $order->getOriginal('status_code')) {
            StatusChangedEvent::dispatch($order);
        }
    }

    /**
     * Handle the order "deleted" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the order "restored" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the order "force deleted" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
