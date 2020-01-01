<?php

namespace App\Events;

use App\Events\EventTypes;
use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProductUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;

    /**
     * Create a new event instance.
     *
     * @param  $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
