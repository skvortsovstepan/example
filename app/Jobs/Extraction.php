<?php

namespace App\Jobs;

use App\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class Extraction implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    private $SomeItem2;

    private $SomeItem5;

    private $extraction_service_id;

    private $SomeItem1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $SomeItem1, $SomeItem2, $SomeItem5, $extraction_service_id)
    {
        $this->data = $data;

        $this->SomeItem2 = $SomeItem2;

        $this->SomeItem5 = $SomeItem5;

        $this->SomeItem1 = $SomeItem1;

        $this->extraction_service_id = $extraction_service_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->SomeItem1->SomeItem2_offline = 1;
        $this->SomeItem1->save();

        if($this->data['is_new']){

            $response = $this->SomeItem1->createNew__SomeItem_1__(
                $this->SomeItem2,
                $this->data['start'],
                $this->data['end'],
                $this->data['all']
            );
        }
        else{
            $response = $this->SomeItem1->extract__Things__FromSomeItem2_(
                $this->SomeItem2,
                $this->data['start'],
                $this->data['end'],
                $this->data['all']
            );
        }

        $this->SomeItem1->SomeItem2_offline = null;

        $this->SomeItem1->save();

        $extraction_service = Service::find($this->extraction_service_id);

        $extraction_service->service_column_one = $response == true ? 1 : null;

        $extraction_service->service_column_three = 1;

        $extraction_service->save();
    }
}
