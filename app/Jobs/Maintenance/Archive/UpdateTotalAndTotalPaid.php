<?php

namespace App\Jobs\Maintenance\Archive;

use App\Models\Order;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateTotalAndTotalPaid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = Order::where(['total' => 0])->limit(500)->latest()->get();

        foreach ($orders as $order) {
            $orderImport = Api2cartOrderImports::where(['order_number' => $order->order_number])->latest()->first();
            if ($orderImport) {
                $attributes = [
                    'total' => $orderImport->raw_import['total']['total'] ?? 0,
                    'total_paid' => $orderImport->raw_import['total']['total_paid'] ?? 0,
                ];

                $order->update($attributes);

                info('UpdateTotalAndTotalPaid: updated totals', ['order_number' => $order->order_number, 'attributes' => $attributes]);
            }
        }

        info('UpdateTotalAndTotalPaid: ran successfully', ["count" => $orders->count()]);
    }
}