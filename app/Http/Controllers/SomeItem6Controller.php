<?php

namespace App\Http\Controllers;

use App\SomeItem2;
use App\SomeItem6;
use App\Jobs\Sync;
use App\Service;
use App\SomeItem5;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Project\Facades\ProjectSync;

class SomeItem6Controller extends Controller
{
    public function orderedLeveledItemThrees()
    {
        return $this->response(SomeItem6::where('SomeItem6_type', Config::get('Projectconfig.depth_type_map')['1'])
            ->where('SomeItem6_offline', null)
            ->withCount('childs')
            //->orderBy('SomeItem6_order', 'asc')
            ->orderByRaw('-SomeItem6_order DESC')->orderBy('SomeItem6_id', 'asc')
            ->get());
    }

    public function SomeItem6sForMerge()
    {
        $sync_service = Service::where('service_name', 'sync')->first();

        if(!$sync_service){
            $new_created_SomeItem6s = [];
            $new_offlined_SomeItem6s = [];
        }
        else{
            $sync_result = unserialize($sync_service->service_data);
            $new_created_SomeItem6s = $sync_result['new_created_SomeItem6s'];
            $new_offlined_SomeItem6s = $sync_result['new_offlined_SomeItem6s'];
        }

        $SomeItem6s_for_merge = [];

        foreach (SomeItem6::whereIn('SomeItem6_id', $new_created_SomeItem6s)->where('SomeItem6_offline', null)/*->orderBy('SomeItem6_order', 'asc')*/->orderByRaw('-SomeItem6_order DESC')->orderBy('SomeItem6_id', 'asc')->get() as $online_SomeItem6){
            $SomeItem6s_for_merge['online_'.$online_SomeItem6->SomeItem6_type.'s'][] = $online_SomeItem6;
        }

        foreach (SomeItem6::/*whereIn('SomeItem6_id', $new_offlined_SomeItem6s)->*/where('SomeItem6_offline', 1)/*->orderBy('SomeItem6_order', 'asc')*/->orderByRaw('-SomeItem6_order DESC')->orderBy('SomeItem6_id', 'asc')->get() as $offline_SomeItem6){
            $SomeItem6s_for_merge['offline_'.$offline_SomeItem6->SomeItem6_type.'s'][] = $offline_SomeItem6;
        }

        return $this->response($SomeItem6s_for_merge);
    }



    public function sync()
    {
        $sync_service = Service::where('service_name', 'sync')->first();

        if(!$sync_service){
            $sync_service = Service::create([
                'service_name' => 'sync',
                'service_description' => 'service_data is responsive for offline/new SomeItem6s after sync;
                                            service_column_one is responsive for last/current sync date;
                                            service_column_two is responsive for % of complete;
                                            service_column_three is responsive for complete flag : null/1 = in_progress/completed'
            ]);
        }

        $sync_result['total_number_of_SomeItem2s']=null;
        $sync_result['new_created_SomeItem6s']=[];
        $sync_result['new_offlined_SomeItem6s']=[];
        $sync_result['last_sync_date']=null;

        // service_data is responsive for offline/new SomeItem6s after sync
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
            'number_of_SomeItem2s'=> null,
            'sync_complete'=> false
        ]);
    }

    public function syncState(){
        $sync_service = Service::where('service_name', 'sync')->first();

            if(!$sync_service){
                $sync_service = Service::create([
                    'service_name' => 'sync',
                    'service_description' => 'service_data is responsive for offline/new SomeItem6s after sync;
                                            service_column_one is responsive for last/current sync date;
                                            service_column_two is responsive for % of complete;
                                            service_column_three is responsive for complete flag : null/1 = in_progress/completed'
                ]);
            }


        if($sync_service->service_column_three == 1){
            $sync_result = unserialize($sync_service->service_data);
            $number_of_SomeItem2s = $sync_result['total_number_of_SomeItem2s'];
            $last_sync = $sync_result['last_sync_date'];

            $sync_data['checked']=0;
            $sync_data['processed']=0;
            $sync_service->service_additional_data = serialize($sync_data);
            $sync_service->save();

            return $this->response([
                'last_sync' => $last_sync,
                'sync_bar_text' => 'Sync is completed',
                'sync_bar_value_now' => '100',
                'number_of_SomeItem2s'=> $number_of_SomeItem2s,
                'sync_complete'=> true
            ]);
        }

        $sync_data = unserialize($sync_service->service_additional_data);

        if($sync_data['checked'] == 0){

            $scan_SomeItem6 = realpath(Config::get('SomeItem2systems.disks')['Project_SomeItem2s_disk']['root']);
            $sync_results = SomeItem6Controller::getDirContents($scan_SomeItem6, $results);
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
            'number_of_SomeItem2s'=> $sync_data['processed'] . " of " . $sync_data['count'],
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

        $SomeItem6_offline = SomeItem6::find($request->data['SomeItem6_offline']);

        $SomeItem6 = SomeItem6::find($request->data['SomeItem6']);

        $merged = ProjectSync::merge($SomeItem6_offline, $SomeItem6);

        if($merged){


            $online_SomeItem6_key = array_search($SomeItem6->SomeItem6_id,  $sync_result['new_created_SomeItem6s']);

            $offline_SomeItem6_key = array_search($SomeItem6->SomeItem6_id,  $sync_result['new_offlined_SomeItem6s']);

            if($online_SomeItem6_key !== false){
                unset($sync_result['new_created_SomeItem6s'][$online_SomeItem6_key]);
            }

            if($offline_SomeItem6_key !== false){
                unset($sync_result['new_offlined_SomeItem6s'][$offline_SomeItem6_key]);
            }

            $sync_service->service_data = serialize($sync_result);

            $sync_service->save();
        }

        return $this->response($merged);
    }

    public function allLeveledItemThrees()
    {
        return $this->response(SomeItem6::where('SomeItem6_type', Config::get('Projectconfig.depth_type_map')['1'])
            ->where('SomeItem6_offline', null)
            ->get()
        );
    }

    public function allLeveledItemOnes()
    {
        return $this->response(SomeItem6::where('SomeItem6_type', Config::get('Projectconfig.depth_type_map')['2'])
            ->where('SomeItem6_offline', null)
            ->with('parent')
            ->get()
        );
    }

    public function allLeveledItemTwos()
    {
        return $this->response(SomeItem6::where('SomeItem6_type', Config::get('Projectconfig.depth_type_map')['3'])
            ->where('SomeItem6_offline', null)
            ->with('parent')
            ->with('parent.parent')
            ->get()
        );
    }

    public function updateDisplayName(Request $request, SomeItem5 $SomeItem5)
    {
        $SomeItem6 = SomeItem6::find($request['pk']);

        $SomeItem6->SomeItem6_display_name = $request['value'];

        $SomeItem6->save();

        return $this->response($SomeItem6->SomeItem6_id);
    }

    public function updateIcon(Request $request, SomeItem5 $SomeItem5)
    {
        $SomeItem6 = SomeItem6::find($request['pk']);

        $SomeItem6->SomeItem6_icon = $request['value'];

        $SomeItem6->save();

        return $this->response($SomeItem6->SomeItem6_id);
    }

    public function updateOrder(Request $request, SomeItem5 $SomeItem5)
    {
        $SomeItem6 = SomeItem6::find($request['pk']);

        $SomeItem6->SomeItem6_order = $request['value'];

        $SomeItem6->save();

        return $this->response($SomeItem6->SomeItem6_id);
    }

    public function getDirContents($dir, &$results = array()){
        global $i;
        $SomeItem2s = scandir($dir);

        foreach($SomeItem2s as $key => $value){
            $path = realpath($dir.'/'.$value);
            if(!is_dir($path)) {

                $path_parts = pathinfo($path);

                $results[$i]['dir'] = $path_parts['dirname'];
                $results[$i]['name'] = $path_parts['SomeItem2name'];
                $results[$i]['ext'] = $path_parts['extension'];

                $i++;

            } else if($value != "." && $value != "..") {
                SomeItem6Controller::getDirContents($path, $results);
                $results[$i]['dir'] = $path;
                $results[$i]['name'] = '';
                $results[$i]['ext'] = 'directory';

                $i++;
            }

        }

        return $results;
    }


}
