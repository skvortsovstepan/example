<?php

namespace App\Http\Controllers;

use App\__some_item_2__;
use App\__some__item__6_;
use App\Jobs\Sync;
use App\Service;
use App\__some__item__5_;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use __project__\Facades\__project__Sync;

class __some__item__6_Controller extends Controller
{
    public function ordered__leveled_item_three__s()
    {
        return $this->response(__some__item__6_::where('__some__item__6__type', Config::get('__project__config.depth_type_map')['1'])
            ->where('__some__item__6__offline', null)
            ->withCount('childs')
            //->orderBy('__some__item__6__order', 'asc')
            ->orderByRaw('-__some__item__6__order DESC')->orderBy('__some__item__6__id', 'asc')
            ->get());
    }

    public function __some__item__6_sForMerge()
    {
        $sync_service = Service::where('service_name', 'sync')->first();

        if(!$sync_service){
            $new_created___some__item__6_s = [];
            $new_offlined___some__item__6_s = [];
        }
        else{
            $sync_result = unserialize($sync_service->service_data);
            $new_created___some__item__6_s = $sync_result['new_created___some__item__6_s'];
            $new_offlined___some__item__6_s = $sync_result['new_offlined___some__item__6_s'];
        }

        $__some__item__6_s_for_merge = [];

        foreach (__some__item__6_::whereIn('__some__item__6__id', $new_created___some__item__6_s)->where('__some__item__6__offline', null)/*->orderBy('__some__item__6__order', 'asc')*/->orderByRaw('-__some__item__6__order DESC')->orderBy('__some__item__6__id', 'asc')->get() as $online___some__item__6_){
            $__some__item__6_s_for_merge['online_'.$online___some__item__6_->__some__item__6__type.'s'][] = $online___some__item__6_;
        }

        foreach (__some__item__6_::/*whereIn('__some__item__6__id', $new_offlined___some__item__6_s)->*/where('__some__item__6__offline', 1)/*->orderBy('__some__item__6__order', 'asc')*/->orderByRaw('-__some__item__6__order DESC')->orderBy('__some__item__6__id', 'asc')->get() as $offline___some__item__6_){
            $__some__item__6_s_for_merge['offline_'.$offline___some__item__6_->__some__item__6__type.'s'][] = $offline___some__item__6_;
        }

        return $this->response($__some__item__6_s_for_merge);
    }



    public function sync()
    {
        $sync_service = Service::where('service_name', 'sync')->first();

        if(!$sync_service){
            $sync_service = Service::create([
                'service_name' => 'sync',
                'service_description' => 'service_data is responsive for offline/new __some__item__6_s after sync;
                                            service_column_one is responsive for last/current sync date;
                                            service_column_two is responsive for % of complete;
                                            service_column_three is responsive for complete flag : null/1 = in_progress/completed'
            ]);
        }

        $sync_result['total_number_of___some_item_2__s']=null;
        $sync_result['new_created___some__item__6_s']=[];
        $sync_result['new_offlined___some__item__6_s']=[];
        $sync_result['last_sync_date']=null;

        // service_data is responsive for offline/new __some__item__6_s after sync
        // service_column_one is responsive for last/current sync date
        // service_column_two is responsive for % of complete
        // service_column_three is responsive for complete flag : null/1

        $sync_data['count']=[];
        $sync_data['checked']=null;
        $sync_data['processed']=0;

        $sync_service->service_data = serialize($sync_result);
        $sync_service->service_additional_data = serialize($sync_data);

        $sync_service->service_column_one = '0';

        $sync_service->service_column_two = 0;

        $sync_service->service_column_three = null;

        $sync_service->save();

        $this->dispatch((new Sync())->onQueue('sync'));

        return $this->response([
            'last_sync' => null,
            'sync_bar_text' => '0%',
            'sync_bar_value_now' => 0,
            'number_of___some_item_2__s'=> null,
            'sync_complete'=> false
        ]);
    }

    public function syncState(){
        $sync_service = Service::where('service_name', 'sync')->first();

            if(!$sync_service){
                $sync_service = Service::create([
                    'service_name' => 'sync',
                    'service_description' => 'service_data is responsive for offline/new __some__item__6_s after sync;
                                            service_column_one is responsive for last/current sync date;
                                            service_column_two is responsive for % of complete;
                                            service_column_three is responsive for complete flag : null/1 = in_progress/completed'
                ]);
            }


        if($sync_service->service_column_three == 1){
            $sync_result = unserialize($sync_service->service_data);
            $number_of___some_item_2__s = $sync_result['total_number_of___some_item_2__s'];
            $last_sync = $sync_result['last_sync_date'];

            $sync_data['checked']=0;
            $sync_data['processed']=0;
            $sync_service->service_additional_data = serialize($sync_data);
            $sync_service->save();

            return $this->response([
                'last_sync' => $last_sync,
                'sync_bar_text' => 'Sync is completed',
                'sync_bar_value_now' => '100',
                'number_of___some_item_2__s'=> $number_of___some_item_2__s,
                'sync_complete'=> true
            ]);
        }

        $sync_data = unserialize($sync_service->service_additional_data);

        if($sync_data['checked'] == 0){

            $scan___some__item__6_ = realpath(Config::get('__some_item_2__systems.disks')['__project_____some_item_2__s_disk']['root']);
            $sync_results = __some__item__6_Controller::getDirContents($scan___some__item__6_, $results);
            $sync_count=0;

            foreach ($sync_results as $sync_result) {

                if ($sync_result['ext'] !== 'directory') {
                    $sync_count++;
                }
            }
            $sync_data['count'] = $sync_count;
            $sync_data['checked'] = 1;
        }

        if ($sync_data['processed'] > 0) {
            $ratio = round($sync_data['processed']/$sync_data['count']*100);
        } else {
            $ratio = 0;
        }

        $sync_service->service_column_two = $ratio;

        $sync_service->service_additional_data = serialize($sync_data);

        $sync_service->save();

        return $this->response([
            'last_sync' => null,
            'sync_bar_text' => $ratio.'%',
            'sync_bar_value_now' => $ratio,
            'number_of___some_item_2__s'=> $sync_data['processed'] . " of " . $sync_data['count'],
            'sync_complete'=> false
        ]);
    }

    public function merge(Request $request)
    {
        $sync_service = Service::where('service_name', 'sync')->first();

        if(!$sync_service){
            return $this->response(false);
        }

        $sync_result = unserialize($sync_service->service_data);

        $__some__item__6__offline = __some__item__6_::find($request->data['__some__item__6__offline']);

        $__some__item__6_ = __some__item__6_::find($request->data['__some__item__6_']);

        $merged = __project__Sync::merge($__some__item__6__offline, $__some__item__6_);

        if($merged){


            $online___some__item__6__key = array_search($__some__item__6_->__some__item__6__id,  $sync_result['new_created___some__item__6_s']);

            $offline___some__item__6__key = array_search($__some__item__6_->__some__item__6__id,  $sync_result['new_offlined___some__item__6_s']);

            if($online___some__item__6__key !== false){
                unset($sync_result['new_created___some__item__6_s'][$online___some__item__6__key]);
            }

            if($offline___some__item__6__key !== false){
                unset($sync_result['new_offlined___some__item__6_s'][$offline___some__item__6__key]);
            }

            $sync_service->service_data = serialize($sync_result);

            $sync_service->save();
        }

        return $this->response($merged);
    }

    public function all__leveled_item_three__s()
    {
        return $this->response(__some__item__6_::where('__some__item__6__type', Config::get('__project__config.depth_type_map')['1'])
            ->where('__some__item__6__offline', null)
            ->get()
        );
    }

    public function all__leveled_item_one__s()
    {
        return $this->response(__some__item__6_::where('__some__item__6__type', Config::get('__project__config.depth_type_map')['2'])
            ->where('__some__item__6__offline', null)
            ->with('parent')
            ->get()
        );
    }

    public function all__leveled_item_two__s()
    {
        return $this->response(__some__item__6_::where('__some__item__6__type', Config::get('__project__config.depth_type_map')['3'])
            ->where('__some__item__6__offline', null)
            ->with('parent')
            ->with('parent.parent')
            ->get()
        );
    }

    public function updateDisplayName(Request $request, __some__item__5_ $__some__item__5_)
    {
        $__some__item__6_ = __some__item__6_::find($request['pk']);

        $__some__item__6_->__some__item__6__display_name = $request['value'];

        $__some__item__6_->save();

        return $this->response($__some__item__6_->__some__item__6__id);
    }

    public function updateIcon(Request $request, __some__item__5_ $__some__item__5_)
    {
        $__some__item__6_ = __some__item__6_::find($request['pk']);

        $__some__item__6_->__some__item__6__icon = $request['value'];

        $__some__item__6_->save();

        return $this->response($__some__item__6_->__some__item__6__id);
    }

    public function updateOrder(Request $request, __some__item__5_ $__some__item__5_)
    {
        $__some__item__6_ = __some__item__6_::find($request['pk']);

        $__some__item__6_->__some__item__6__order = $request['value'];

        $__some__item__6_->save();

        return $this->response($__some__item__6_->__some__item__6__id);
    }

    public function getDirContents($dir, &$results = array()){
        global $i;
        $__some_item_2__s = scandir($dir);

        foreach($__some_item_2__s as $key => $value){
            $path = realpath($dir.'/'.$value);
            if(!is_dir($path)) {

                $path_parts = pathinfo($path);

                $results[$i]['dir'] = $path_parts['dirname'];
                $results[$i]['name'] = $path_parts['__some_item_2__name'];
                $results[$i]['ext'] = $path_parts['extension'];

                $i++;

            } else if($value != "." && $value != "..") {
                __some__item__6_Controller::getDirContents($path, $results);
                $results[$i]['dir'] = $path;
                $results[$i]['name'] = '';
                $results[$i]['ext'] = 'directory';

                $i++;
            }

        }

        return $results;
    }


}
