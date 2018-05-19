<?php

namespace App\Http\Controllers;

use App\SomeItem2;
use App\SomeItem2Flag;
use App\SomeItem6;
use App\Jobs\Extraction;
use App\SomeItem1;
use App\Service;
use App\SomeItem9;
use App\SomeItem5;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use m1r1k\SejdaConsole\Sejda;

class someItem2Controller extends Controller
{
    public function SomeItem7s(SomeItem2 $SomeItem2, SomeItem5 $SomeItem5)
    {
        return $this->response($SomeItem2->approvedSomeItem7s()->get());
    }

    public function toggleFavorite(SomeItem2 $SomeItem2, SomeItem5 $SomeItem5)
    {
        $SomeItem2->toggleFavorite($SomeItem5);

        return $this->response($SomeItem2->favorites()->count());
    }

    public function toggleWish(SomeItem2 $SomeItem2, SomeItem5 $SomeItem5)
    {
        $SomeItem2->toggleWish($SomeItem5);

        return $this->response($SomeItem2->wishes()->count());
    }

    public function toggleHidden(SomeItem2 $SomeItem2, SomeItem1 $SomeItem1, SomeItem5 $SomeItem5)
    {
        $SomeItem2->toggleHidden($SomeItem1, $SomeItem5);

        return $this->response('toggled');
    }


    public function downloadSomeItem2($SomeItem2)
    {
        if($SomeItem2->SomeItem2_offline == 1){
            return Response::make('SomeItem2 is offline', 404);
        }

        if($SomeItem2->isPdf()){

                return response()
                    ->SomeItem2($SomeItem2->getRealPath());

        }
        elseif($SomeItem2->isAllowedExtension()){
            return SomeItem2_get_contents($SomeItem2->getRealPath());
        }

        return response()->download($SomeItem2->getRealPath());
    }

    public function getLeveledItemThreesAndLeveledItemOnes(SomeItem5 $SomeItem5){
        return $this->response(SomeItem6::where('SomeItem6_parent', null)
            ->where('SomeItem6_offline', null)
            ->with(['childs' => function ($query) {
                $query->where('SomeItem6_offline', null);
        }])->get());
    }

    public function SomeItem1sWithSomeItem4s(SomeItem6 $SomeItem6, SomeItem5 $SomeItem5)
    {
        $SomeItem5_LeveledItemOne_SomeItem4s = SomeItem2::where('SomeItem2_is_SomeItem4', 1)
            ->where('SomeItem2_SomeItem6_id', $SomeItem6->SomeItem6_id)
            //->where('SomeItem2_offline', null)
            ->where('SomeItem2_path', 'like', $SomeItem5->SomeItem5_id.'/%')
            ->get();

        $pre_data = [];

        foreach ($SomeItem5_LeveledItemOne_SomeItem4s as $SomeItem4){

            $SomeItem1 = $SomeItem4->SomeItem4SomeItem1();

            $SomeItem1_id = $SomeItem1 == null? 'unrelated' : $SomeItem1->SomeItem1_id;

            $SomeItem1_name = $SomeItem1 == null? 'Unrelated SomeItem4s' : $SomeItem1->SomeItem1_name;

            $pre_data['SomeItem1_'.$SomeItem1_id]['SomeItem1_name']=$SomeItem1_name;

            $pre_data['SomeItem1_'.$SomeItem1_id]['SomeItem1_SomeItem5_SomeItem4s'][]=$SomeItem4;

        }

        $data = [];

        ksort($pre_data);

        foreach ($pre_data as $item){
            $data[]=$item;
        }

        return $this->response($data);
    }

    public function extractSomeItem2(Request $request, SomeItem2 $SomeItem2, SomeItem5 $SomeItem5)
    {

        $extraction_service = Service::create([
            'service_name' => 'extraction',
            'service_description' => 'service_column_one is responsive for result of extraction, 
                                      service_column_three is responsive for complete flag : null/1 = in_progress/completed',
            'service_column_three'=> null,
        ]);


        $data = $request->data['extraction_data'];


        if($data['is_new']){

            $SomeItem4 = new SomeItem2();

            $timestamp=date('YmdHis');

            if(SomeItem2::where('SomeItem2_name', $data['value'].$timestamp.'.pdf')->count() >= 1){
                $iterator = 1;

                while(SomeItem2::where('SomeItem2_name', $data['value'].$timestamp.'('.$iterator.')'.'.pdf')->count() >= 1){
                    $iterator++;
                }

                $SomeItem4->SomeItem2_name = $data['value'].$timestamp.'('.$iterator.')'.'.pdf';

            }
            else{
                $SomeItem4->SomeItem2_name = $data['value'].$timestamp.'.pdf';
            }

            $SomeItem4->SomeItem2_display_name = $data['value'];

            $SomeItem4->SomeItem2_is_SomeItem4 = 1;

            $SomeItem4->SomeItem2_SomeItem6_id = $data['LeveledItemOne_id'];

            $SomeItem1_id_for_path = $data['SomeItem1_id'] == null ? 'Unrelated' : $data['SomeItem1_id'];

            $SomeItem4->SomeItem2_path = $SomeItem5->SomeItem5_id.'/SomeItem4s/'.$SomeItem1_id_for_path;

            $SomeItem4->save();
        }
        else{
            $SomeItem4=SomeItem2::find($data['value']);
        }

        $extraction_service_id = $extraction_service->service_id;

        $this->dispatch((new Extraction($data, $SomeItem4, $SomeItem2, $SomeItem5, $extraction_service_id))->onQueue('extraction'));

        return $this->response(['result'=>true, 'service_id' => $extraction_service->service_id, 'SomeItem4_id' => $SomeItem4->SomeItem2_id]);
    }

    public function pendingExtractionState($service_id, SomeItem5 $SomeItem5)
    {
        $extraction_service = Service::find($service_id);

        if ($extraction_service->service_column_three == 1){
            $result = $extraction_service->service_column_one;

            $extraction_service->delete();

            return $this->response(['extract_complete'=>true, 'success' => $result]);
        }

        return $this->response(['extract_complete'=>false, 'result' => false]);
    }

    public function updateDisplayName(Request $request, SomeItem2 $SomeItem2, SomeItem5 $SomeItem5)
    {
        $SomeItem2_name = trim($request->value);

        $SomeItem2->SomeItem2_display_name = $SomeItem2_name;

        $SomeItem2->save();

        return $this->response($SomeItem2->SomeItem2_id);
    }

    public function deleteSomeItem4(Request $request, SomeItem2 $SomeItem2, SomeItem5 $SomeItem5)
    {
        return $this->response($SomeItem2->deleteSomeItem4());
    }

    public function updateDescription(Request $request, SomeItem2 $SomeItem2, SomeItem5 $SomeItem5)
    {
        $SomeItem2_description = trim($request->value);

        $SomeItem2->SomeItem2_description = $SomeItem2_description;

        $SomeItem2->save();

        return $this->response($SomeItem2->SomeItem2_id);
    }


    public function updateFlagReason(Request $request, SomeItem2 $SomeItem2, SomeItem5 $SomeItem5)
    {
        $SomeItem2->SomeItem2_flagged = 1;

        $SomeItem2->SomeItem2_flag_reason = $request->value;

        $SomeItem2->save();

        return $this->response($SomeItem2->SomeItem2_id);
    }

    public function unflag(Request $request, SomeItem2 $SomeItem2, SomeItem5 $SomeItem5)
    {
        $SomeItem2->SomeItem2_flagged = null;

        $SomeItem2->SomeItem2_flag_reason = null;

        $SomeItem2->save();

        return $this->response($SomeItem2->SomeItem2_id);
    }

    public function updateSomeItem9(Request $request, SomeItem2 $SomeItem2, SomeItem5 $SomeItem5)
    {
        $SomeItem9 = $request->data['SomeItem9'];

        $SomeItem9_db = $SomeItem2->SomeItem9s()->where('SomeItem9_id', $SomeItem9['id'])->first();

        if($SomeItem9['checkbox']=='on'){
            $SomeItem9['value'];

            $SomeItem9['id'];

            if(!$SomeItem9_db){
                $SomeItem2->SomeItem9s()->attach($SomeItem9['id'], ['SomeItem2_SomeItem9_value'=>$SomeItem9['value'], 'SomeItem2_SomeItem9_SomeItem5'=>$SomeItem5->SomeItem5_id]);
            }
            else{
                $SomeItem2->SomeItem9s()->updateExistingPivot($SomeItem9['id'], ['SomeItem2_SomeItem9_value'=>$SomeItem9['value'], 'SomeItem2_SomeItem9_SomeItem5'=>$SomeItem5->SomeItem5_id]);
            }
        }
        elseif($SomeItem9['checkbox']=='off' && $SomeItem9_db!=null){
            $SomeItem2->SomeItem9s()->detach($SomeItem9['id']);
        }

        return $this->response($SomeItem2->SomeItem9s()->get());
    }

    public function updateLock(Request $request, SomeItem2 $SomeItem2, SomeItem5 $SomeItem5)
    {

        if($request->data['lock']){
            $SomeItem2->lock($SomeItem5);
        }
        else{
            $SomeItem2->unlock();;
        }

        return $this->response(true);
    }

    public function thumbnail(SomeItem2 $SomeItem2)
    {
        return $SomeItem2->thumbnail();
    }

    public function deleteSomeItem2(Request $request, SomeItem5 $SomeItem5)
    {
        $SomeItem2 = SomeItem2::find($request->data['SomeItem2_id']);

        if(!$SomeItem2){
            return response('', 404);
        }

        $SomeItem2->deleteSomeItem2();

        return $this->response(true);
    }

    public function uploadRandomSomeItem2s(Request $request, SomeItem5 $SomeItem5)
    {
        $SomeItem2s_to_take = Config::get('Projectconfig.random_SomeItem2s_count');

        $SomeItem2s=[];

        if(!isset($request->data['option'])){
            return response('Not found', 404);
        }
        elseif($request->data['option'] == 'wish'){
            $SomeItem2s = $SomeItem5->wishs()
                ->with(array(
                    'parentSomeItem6'=>function($query){
                    $query->select('SomeItem6_id', 'SomeItem6_name', 'SomeItem6_display_name');
                }))
                ->inRandomOrder()
                ->take($SomeItem2s_to_take)
                ->get();
        }
        elseif($request->data['option'] == 'fav'){
            $SomeItem2s = $SomeItem5->favs()
                ->with(array(
                    'parentSomeItem6'=>function($query){
                        $query->select('SomeItem6_id', 'SomeItem6_name', 'SomeItem6_display_name');
                    }))
                ->inRandomOrder()
                ->take($SomeItem2s_to_take)
                ->get();
        }
        else{
            return response('Not found', 404);
        }

        $response=[];

        foreach ($SomeItem2s as $SomeItem2){
            $response[]=[
                /*
                 * Hidden
                 * */
            ];
        }

        return $this->response($response);
    }

    public function flaggedSomeItem2s()
    {
        return $this->response(SomeItem2::where('SomeItem2_flagged', 1)->where('SomeItem2_is_SomeItem4', null)->with('SomeItem2Flag')->get());
    }

}
