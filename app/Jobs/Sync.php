<?php

namespace App\Jobs;

use App\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class Sync implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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
        $sync_service = Service::where('service_name', 'sync')->first();

        $sync_date = date('YmdHis');

        $sync_service->service_column_one = $sync_date;

        $sync_service->save();

        $sync_result = __project__Sync::sync(Storage::disk(\Config::get('__some_config__')), $sync_date);

        $sync_service->service_data = serialize($sync_result);

        $sync_service->service_column_three = 1;

        $sync_service->save();

    }
}
