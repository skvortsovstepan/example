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

    private $__some_item_2__;

    private $__some__item__5_;

    private $extraction_service_id;

    private $__some__item_1__;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $__some__item_1__, $__some_item_2__, $__some__item__5_, $extraction_service_id)
    {
        $this->data = $data;

        $this->__some_item_2__ = $__some_item_2__;

        $this->__some__item__5_ = $__some__item__5_;

        $this->__some__item_1__ = $__some__item_1__;

        $this->extraction_service_id = $extraction_service_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->__some__item_1__->__some_item_2___offline = 1;
        $this->__some__item_1__->save();

        if($this->data['is_new']){

            $response = $this->__some__item_1__->createNew__SomeItem_1__(
                $this->__some_item_2__,
                $this->data['start'],
                $this->data['end'],
                $this->data['all']
            );
        }
        else{
            $response = $this->__some__item_1__->extract__Things__From__some_item_2___(
                $this->__some_item_2__,
                $this->data['start'],
                $this->data['end'],
                $this->data['all']
            );
        }

        $this->__some__item_1__->__some_item_2___offline = null;

        $this->__some__item_1__->save();

        $extraction_service = Service::find($this->extraction_service_id);

        $extraction_service->service_column_one = $response == true ? 1 : null;

        $extraction_service->service_column_three = 1;

        $extraction_service->save();
    }
}
