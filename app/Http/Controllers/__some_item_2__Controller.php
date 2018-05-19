<?php

namespace App\Http\Controllers;

use App\__some_item_2__;
use App\__some_item_2__Flag;
use App\__some__item__6_;
use App\Jobs\Extraction;
use App\__some__item_1__;
use App\Service;
use App\__some_item_9__;
use App\__some__item__5_;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use m1r1k\SejdaConsole\Sejda;

class __some_item_2__Controller extends Controller
{
    public function __some_item_7__s(__some_item_2__ $__some_item_2__, __some__item__5_ $__some__item__5_)
    {
        return $this->response($__some_item_2__->approved__some_item_7__s()->get());
    }

    public function toggleFavorite(__some_item_2__ $__some_item_2__, __some__item__5_ $__some__item__5_)
    {
        $__some_item_2__->toggleFavorite($__some__item__5_);

        return $this->response($__some_item_2__->favorites()->count());
    }

    public function toggleWish(__some_item_2__ $__some_item_2__, __some__item__5_ $__some__item__5_)
    {
        $__some_item_2__->toggleWish($__some__item__5_);

        return $this->response($__some_item_2__->wishes()->count());
    }

    public function toggleHidden(__some_item_2__ $__some_item_2__, __some__item_1__ $__some__item_1__, __some__item__5_ $__some__item__5_)
    {
        $__some_item_2__->toggleHidden($__some__item_1__, $__some__item__5_);

        return $this->response('toggled');
    }


    public function download__some_item_2__($__some_item_2__)
    {
        if($__some_item_2__->__some_item_2___offline == 1){
            return Response::make('__some_item_2__ is offline', 404);
        }

        if($__some_item_2__->isPdf()){

                return response()
                    ->__some_item_2__($__some_item_2__->getRealPath());

        }
        elseif($__some_item_2__->isAllowedExtension()){
            return __some_item_2___get_contents($__some_item_2__->getRealPath());
        }

        return response()->download($__some_item_2__->getRealPath());
    }

    public function get__leveled_item_three__sAnd__leveled_item_one__s(__some__item__5_ $__some__item__5_){
        return $this->response(__some__item__6_::where('__some__item__6__parent', null)
            ->where('__some__item__6__offline', null)
            ->with(['childs' => function ($query) {
                $query->where('__some__item__6__offline', null);
        }])->get());
    }

    public function __some__item_1__sWith__some_item_4__s(__some__item__6_ $__some__item__6_, __some__item__5_ $__some__item__5_)
    {
        $__some__item__5____leveled_item_one_____some_item_4__s = __some_item_2__::where('__some_item_2___is___some_item_4__', 1)
            ->where('__some_item_2_____some__item__6__id', $__some__item__6_->__some__item__6__id)
            //->where('__some_item_2___offline', null)
            ->where('__some_item_2___path', 'like', $__some__item__5_->__some__item__5__id.'/%')
            ->get();

        $pre_data = [];

        foreach ($__some__item__5____leveled_item_one_____some_item_4__s as $__some_item_4__){

            $__some__item_1__ = $__some_item_4__->__some_item_4____some__item_1__();

            $__some__item_1___id = $__some__item_1__ == null? 'unrelated' : $__some__item_1__->__some__item_1___id;

            $__some__item_1___name = $__some__item_1__ == null? 'Unrelated __some_item_4__s' : $__some__item_1__->__some__item_1___name;

            $pre_data['__some__item_1___'.$__some__item_1___id]['__some__item_1___name']=$__some__item_1___name;

            $pre_data['__some__item_1___'.$__some__item_1___id]['__some__item_1_____some__item__5____some_item_4__s'][]=$__some_item_4__;

        }

        $data = [];

        ksort($pre_data);

        foreach ($pre_data as $item){
            $data[]=$item;
        }

        return $this->response($data);
    }

    public function extract__some_item_2__(Request $request, __some_item_2__ $__some_item_2__, __some__item__5_ $__some__item__5_)
    {

        $extraction_service = Service::create([
            'service_name' => 'extraction',
            'service_description' => 'service_column_one is responsive for result of extraction, 
                                      service_column_three is responsive for complete flag : null/1 = in_progress/completed',
            'service_column_three'=> null,
        ]);


        $data = $request->data['extraction_data'];


        if($data['is_new']){

            $__some_item_4__ = new __some_item_2__();

            $timestamp=date('YmdHis');

            if(__some_item_2__::where('__some_item_2___name', $data['value'].$timestamp.'.pdf')->count() >= 1){
                $iterator = 1;

                while(__some_item_2__::where('__some_item_2___name', $data['value'].$timestamp.'('.$iterator.')'.'.pdf')->count() >= 1){
                    $iterator++;
                }

                $__some_item_4__->__some_item_2___name = $data['value'].$timestamp.'('.$iterator.')'.'.pdf';

            }
            else{
                $__some_item_4__->__some_item_2___name = $data['value'].$timestamp.'.pdf';
            }

            $__some_item_4__->__some_item_2___display_name = $data['value'];

            $__some_item_4__->__some_item_2___is___some_item_4__ = 1;

            $__some_item_4__->__some_item_2_____some__item__6__id = $data['__leveled_item_one___id'];

            $__some__item_1___id_for_path = $data['__some__item_1___id'] == null ? 'Unrelated' : $data['__some__item_1___id'];

            $__some_item_4__->__some_item_2___path = $__some__item__5_->__some__item__5__id.'/__some_item_4__s/'.$__some__item_1___id_for_path;

            $__some_item_4__->save();
        }
        else{
            $__some_item_4__=__some_item_2__::find($data['value']);
        }

        $extraction_service_id = $extraction_service->service_id;

        $this->dispatch((new Extraction($data, $__some_item_4__, $__some_item_2__, $__some__item__5_, $extraction_service_id))->onQueue('extraction'));

        return $this->response(['result'=>true, 'service_id' => $extraction_service->service_id, '__some_item_4___id' => $__some_item_4__->__some_item_2___id]);
    }

    public function pendingExtractionState($service_id, __some__item__5_ $__some__item__5_)
    {
        $extraction_service = Service::find($service_id);

        if ($extraction_service->service_column_three == 1){
            $result = $extraction_service->service_column_one;

            $extraction_service->delete();

            return $this->response(['extract_complete'=>true, 'success' => $result]);
        }

        return $this->response(['extract_complete'=>false, 'result' => false]);
    }

    public function updateDisplayName(Request $request, __some_item_2__ $__some_item_2__, __some__item__5_ $__some__item__5_)
    {
        $__some_item_2___name = trim($request->value);

        $__some_item_2__->__some_item_2___display_name = $__some_item_2___name;

        $__some_item_2__->save();

        return $this->response($__some_item_2__->__some_item_2___id);
    }

    public function delete__some_item_4__(Request $request, __some_item_2__ $__some_item_2__, __some__item__5_ $__some__item__5_)
    {
        return $this->response($__some_item_2__->delete__some_item_4__());
    }

    public function updateDescription(Request $request, __some_item_2__ $__some_item_2__, __some__item__5_ $__some__item__5_)
    {
        $__some_item_2___description = trim($request->value);

        $__some_item_2__->__some_item_2___description = $__some_item_2___description;

        $__some_item_2__->save();

        return $this->response($__some_item_2__->__some_item_2___id);
    }


    public function updateFlagReason(Request $request, __some_item_2__ $__some_item_2__, __some__item__5_ $__some__item__5_)
    {
        $__some_item_2__->__some_item_2___flagged = 1;

        $__some_item_2__->__some_item_2___flag_reason = $request->value;

        $__some_item_2__->save();

        return $this->response($__some_item_2__->__some_item_2___id);
    }

    public function unflag(Request $request, __some_item_2__ $__some_item_2__, __some__item__5_ $__some__item__5_)
    {
        $__some_item_2__->__some_item_2___flagged = null;

        $__some_item_2__->__some_item_2___flag_reason = null;

        $__some_item_2__->save();

        return $this->response($__some_item_2__->__some_item_2___id);
    }

    public function update__some_item_9__(Request $request, __some_item_2__ $__some_item_2__, __some__item__5_ $__some__item__5_)
    {
        $__some_item_9__ = $request->data['__some_item_9__'];

        $__some_item_9___db = $__some_item_2__->__some_item_9__s()->where('__some_item_9___id', $__some_item_9__['id'])->first();

        if($__some_item_9__['checkbox']=='on'){
            $__some_item_9__['value'];

            $__some_item_9__['id'];

            if(!$__some_item_9___db){
                $__some_item_2__->__some_item_9__s()->attach($__some_item_9__['id'], ['__some_item_2_____some_item_9___value'=>$__some_item_9__['value'], '__some_item_2_____some_item_9_____some__item__5_'=>$__some__item__5_->__some__item__5__id]);
            }
            else{
                $__some_item_2__->__some_item_9__s()->updateExistingPivot($__some_item_9__['id'], ['__some_item_2_____some_item_9___value'=>$__some_item_9__['value'], '__some_item_2_____some_item_9_____some__item__5_'=>$__some__item__5_->__some__item__5__id]);
            }
        }
        elseif($__some_item_9__['checkbox']=='off' && $__some_item_9___db!=null){
            $__some_item_2__->__some_item_9__s()->detach($__some_item_9__['id']);
        }

        return $this->response($__some_item_2__->__some_item_9__s()->get());
    }

    public function updateLock(Request $request, __some_item_2__ $__some_item_2__, __some__item__5_ $__some__item__5_)
    {

        if($request->data['lock']){
            $__some_item_2__->lock($__some__item__5_);
        }
        else{
            $__some_item_2__->unlock();;
        }

        return $this->response(true);
    }

    public function thumbnail(__some_item_2__ $__some_item_2__)
    {
        return $__some_item_2__->thumbnail();
    }

    public function delete__some_item_2__(Request $request, __some__item__5_ $__some__item__5_)
    {
        $__some_item_2__ = __some_item_2__::find($request->data['__some_item_2___id']);

        if(!$__some_item_2__){
            return response('', 404);
        }

        $__some_item_2__->delete__some_item_2__();

        return $this->response(true);
    }

    public function uploadRandom__some_item_2__s(Request $request, __some__item__5_ $__some__item__5_)
    {
        $__some_item_2__s_to_take = Config::get('__project__config.random___some_item_2__s_count');

        $__some_item_2__s=[];

        if(!isset($request->data['option'])){
            return response('Not found', 404);
        }
        elseif($request->data['option'] == 'wish'){
            $__some_item_2__s = $__some__item__5_->wishs()
                ->with(array(
                    'parent__some__item__6_'=>function($query){
                    $query->select('__some__item__6__id', '__some__item__6__name', '__some__item__6__display_name');
                }))
                ->inRandomOrder()
                ->take($__some_item_2__s_to_take)
                ->get();
        }
        elseif($request->data['option'] == 'fav'){
            $__some_item_2__s = $__some__item__5_->favs()
                ->with(array(
                    'parent__some__item__6_'=>function($query){
                        $query->select('__some__item__6__id', '__some__item__6__name', '__some__item__6__display_name');
                    }))
                ->inRandomOrder()
                ->take($__some_item_2__s_to_take)
                ->get();
        }
        else{
            return response('Not found', 404);
        }

        $response=[];

        foreach ($__some_item_2__s as $__some_item_2__){
            $response[]=[
                /*
                 * Hidden
                 * */
            ];
        }

        return $this->response($response);
    }

    public function flagged__some_item_2__s()
    {
        return $this->response(__some_item_2__::where('__some_item_2___flagged', 1)->where('__some_item_2___is___some_item_4__', null)->with('__some_item_2__Flag')->get());
    }

}
