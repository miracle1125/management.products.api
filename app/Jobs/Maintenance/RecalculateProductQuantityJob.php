<?php

namespace App\Jobs\Maintenance;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RecalculateProductQuantityJob implements ShouldQueue
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
        $prefix = config('database.connections.mysql.prefix');

        DB::statement('
            UPDATE `'.$prefix.'products`

            SET `'.$prefix.'products`.`quantity` = (
                SELECT sum(quantity)
                FROM `'.$prefix.'inventory`
                WHERE `'.$prefix.'inventory`.`product_id` = `'.$prefix.'products`.`id`
            )
        ');
    }
}