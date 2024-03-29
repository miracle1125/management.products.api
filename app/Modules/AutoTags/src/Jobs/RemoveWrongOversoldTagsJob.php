<?php

namespace App\Modules\AutoTags\src\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class RemoveWrongOversoldTagsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Starting RemoveWrongOversoldTagsJob');

        $invalidProducts = Product::withAllTags(['oversold'])
            ->where('quantity_available', '>=', 0)
            ->get()
            ->each(function (Product $product) {
                $product->detachTag('oversold');
            });

        info('Finished RemoveWrongOversoldTagsJob', ['records_corrected' => $invalidProducts->count()]);
    }
}
