<?php

namespace App\Http\Controllers;

use App\__some_item_2__;
use App\__some__item__6_;
use App\__some_item_7__;
use App\__some__item_1__;
use App\__some__item_1__sGroup;
use App\__some__item__5_;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Imagick;
use PDO;
use __project__\Facades\__some__auth_service__;
use __project__\Facades\__project__Sync;

class __some__item_1__Controller extends Controller
{
    public function builderSteps(__some__item__5_ $__some__item__5_)
    {
        $__some__item__6_s = __some__item__6_::where('__some__item__6__type', '__leveled_item_three__')->where('__some__item__6__offline', null)/*->orderBy('__some__item__6__order', 'asc')*/->orderByRaw('-__some__item__6__order DESC')->orderBy('__some__item__6__id', 'asc')->get();

        $data = [['type'=>'__some_item_9__s', 'url'=>'__some_item_9__s', 'number'=>1, 'name'=>'__some_item_9__s']];

        // TODO it is the mock, than it will be defined by __some__item__6__number field
        $i = 1;

        foreach($__some__item__6_s as $__some__item__6_){
            $data[]=[
                'type'=>'__leveled_item_three__',
                'id'=>$__some__item__6_->__some__item__6__id,
                'number'=>++$i,
                'name'=>empty($__some__item__6_->__some__item__6__display_name)
                    ? ucwords(str_replace('_', ' ', $__some__item__6_->__some__item__6__name), ' ')
                    : $__some__item__6_->__some__item__6__display_name
            ];
        }

        return $this->response($data);
    }


    public function uploadImgTmp(Request $request, __some__item__5_ $__some__item__5_)
    {
        $__some_item_2__ = __some_item_2___get_contents($request->__some_item_2__('__some_item_2__')->getRealPath());
        $original___some_item_2___name = $request->__some_item_2__('__some_item_2__')->getClientOriginalName();

        $disk = Storage::disk(Config::get('__project__config.__project___tmp_disk'));

        $timestamp=date('YmdHis');

        $__some_item_2___name = $original___some_item_2___name.'_'.$timestamp.'_'.__some__auth_service__::id();

        $disk->put($__some_item_2___name, $__some_item_2__);

        return $this->response($__some_item_2___name);
    }


    public function save(Request $request)
    {
        $__some__item_1__ = null;

        $data = $request->data;

        foreach ($data as $key => $item) {
            if($key=='0'){
                $__some__item_1__ = __some__item_1__::buildOrUpdate($item);
            }
            else{
                $__some__item_1__->attach__leveled_item_two__s($item);
            }
        }

        return $this->response($__some__item_1__->__some__item_1___id);
    }


    public function __some__item_1__(__some__item_1__ $__some__item_1__, $__some__item__5_)
    {
        $group =$__some__item_1__->group()->first();

        $customized___some__item_1__s = __some__item_1__::whereRaw('__some__item_1___name RLIKE CONCAT(?, "[0-9]*")', [$__some__item_1__->__some__item_1___name.' - Customized '])
            ->where('__some__item_1___owner_id', $__some__item__5_->__some__item__5__id);

        if($customized___some__item_1__s->first() == null){

            if (__some__item_1__::where('__some__item_1___name', $__some__item_1__->__some__item_1___name.' - Customized')
                    ->where('__some__item_1___owner_id', $__some__item__5_->__some__item__5__id)->first()!=null
            ){
                $default_name = $__some__item_1__->__some__item_1___name.' - Customized 2';
            }
            else{
                $default_name = $__some__item_1__->__some__item_1___name.' - Customized';
            }
        }
        else{
            $default_name = $customized___some__item_1__s->orderBy('__some__item_1___name', 'desc')->first()->__some__item_1___name;

            $number = (int) substr($default_name, strripos($default_name, ' '));

            $number++;

            $default_name = substr($default_name, 0, strripos($default_name, ' ')+1).$number;

        }

        $pre___some_item_9__s = [];

        $__some_item_9__s = $__some__item_1__->__some_item_9__s()->get();

        foreach ($__some_item_9__s as $__some_item_9__){
            $pre___some_item_9__s['__some_item_9___'.$__some_item_9__->__some_item_9___id]=[
                'id' => $__some_item_9__->__some_item_9___id,
                'value' => $__some_item_9__->pivot->__some__item_1___meta___some_item_9___value
            ];
        }

        $default_state = [
            '__some_item_9__s_step'=>[
                'name'=>$default_name,
                'description'=>$__some__item_1__->__some__item_1___description,
                'group'=>[
                    'id'=>$group == null ? null : $group->__some__item_1___group_id,
                    'name'=>$group == null ? null : $group->__some__item_1___group_display_name
                ],
                'pre___some_item_9__s' => $pre___some_item_9__s,
                '__some__item_1___id' =>$__some__item_1__->__some__item_1___id,
                '__some__item_1___initial_name' => $__some__item_1__->__some__item_1___name,
                '__some__item_1___default_icon' => $__some__item_1__->__some__item_1___icon
            ]
        ];


        foreach($__some__item_1__->__leveled_item_two__s()->with('parent')->with('parent.parent')->get() as $__leveled_item_two__){
            $default_state['__leveled_item_three___'.$__leveled_item_two__->parent->parent->__some__item__6__id]['__leveled_item_one___'.$__leveled_item_two__->parent->__some__item__6__id][]
                = $__leveled_item_two__->__some__item__6__id;
        }

        foreach ($__some__item_1__->__leveled_item_three__s()->get() as $__leveled_item_three__){
            $default_state['__leveled_item_three___'.$__leveled_item_three__->__some__item__6__id]['volume_slider'] = $__leveled_item_three__->pivot->__some__item_1___meta_results_volume;
        }


        return $this->response([
            '__some__item_1__'=>$__some__item_1__,
            'group'=>$group,
            '__some__item_1_____leveled_item_three__s'=>$__some__item_1__->__leveled_item_three__s()->withCount('childs')->orderByRaw('-__some__item__6__order DESC')->orderBy('__some__item__6__id', 'asc')->get(),
            '__some__item_1_____some_item_9__s'=> $__some_item_9__s,
            '__some__item_1___average___some_item_8__'=>$__some__item_1__->average__some_item_8__(),
            '__some__item_1___number_votes'=>$__some__item_1__->__some_item_8__s()->count(),
            '__some__item_1___number___some_item_7__s'=>$__some__item_1__->approved__some_item_7__s()->count(),
            '__some__item_1_____some__item__5____some_item_8__' => $__some__item_1__->__some__item__5___some_item_8__($__some__item__5_)==null ? null : $__some__item_1__->__some__item__5___some_item_8__($__some__item__5_)->__some_item_8_____some_item_8__,
            '__some__item_1_____some__item__5____some_item_7__' => $__some__item_1__->__some__item__5___some_item_7__($__some__item__5_)==null ? null : $__some__item_1__->__some__item__5___some_item_7__($__some__item__5_)->__some_item_7___content,
            '__some__item_1___default_state' => $default_state,
            '__some__item_1___is_owner' => $__some__item_1__->__some__item_1___owner_id == $__some__item__5_->__some__item__5__id || in_array($__some__item__5_->__some__item__5__id, Config::get('__project__config.default___some__item__5_s_ids')) ? true : false,
            '__some__item_1___number___some_item_2__s' => $__some__item_1__->__some_item_2__sCount(),
            '__some__item_1___number___leveled_item_two__s' => $__some__item_1__->__leveled_item_two__sCount(),
            '__some__item_1___number___leveled_item_one__s' => $__some__item_1__->__leveled_item_one__sCount(),
            '__some__item_1___number___leveled_item_three__s' => $__some__item_1__->__leveled_item_three__sCount(),
        ]);
    }

    public function __some__item_1__s(Request $request, __some__item__5_ $__some__item__5_)
    {
        $type = $request->data['type'];
        $group_id = $request->data['group_id'];
        $sorting_option = $request->data['sorting_option'];
        $page = $request->data['page'];
        $pagination = $request->data['pagination'];

        $sorting_map = [
            'default',
            'relevance',
            '__leveled_item_three__s_number',
            '__leveled_item_one__s_number',
            '__leveled_item_two__s_number',
            '__some_item_2__s_number',
            'average___some_item_8__',
            '__some_item_7__s_number'
        ];

        $sorting_option = isset($sorting_map[$sorting_option]) ? $sorting_map[$sorting_option] : 'default';

        $list = __some__item_1__::sortAndPaginate($type, $sorting_option, $group_id, $page, $pagination, $__some__item__5_);

        return $this->response($list);
    }


    // Option defines which part of frontend calls this method
    public function __leveled_item_three__InfoOld(__some__item__6_ $__leveled_item_three__, $__some_item_9__s, $volume, $__some__item_1__, $option, __some__item__5_ $__some__item__5_)
    {
        $data = [];

        foreach ($__leveled_item_three__->childs()->where('__some__item__6__offline', null)/*->orderBy('__some__item__6__order', 'asc')*/->orderByRaw('-__some__item__6__order DESC')->orderBy('__some__item__6__id', 'asc')->get() as $__leveled_item_one__) {

            if($option == '__some_item_4__s'){
                $number_of___leveled_item_one_____some_item_2__s = __some_item_2__::where('__some_item_2_____some__item__6__id', $__leveled_item_one__->__some__item__6__id)
                    ->where('__some_item_2___is___some_item_4__', 1)
                    ->where('__some_item_2___offline', null)
                    ->where('__some_item_2___path', 'like', $__some__item__5_!=null ? $__some__item__5_->__some__item__5__id.'/%' : null)->count();

                if($number_of___leveled_item_one_____some_item_2__s!=0) {
                    $data[] = [
                        '__some__item__6__id' => $__leveled_item_one__->__some__item__6__id,
                        '__some__item__6__name' => $__leveled_item_one__->__some__item__6__name,
                        '__some__item__6__display_name' => $__leveled_item_one__->__some__item__6__display_name,
                        '__some__item__6__icon' => $__leveled_item_one__->__some__item__6__icon,
                        '__some__item__6__number___some_item_2__s' => $number_of___leveled_item_one_____some_item_2__s,
                        '__some__item__6__childs' => []
                    ];
                }
                continue;
            }



            $__leveled_item_two__s= [];

            $number_of___leveled_item_one_____some_item_2__s = 0;

            if($__some__item_1__!==null) {
                if($__some__item_1__->does__leveled_item_one__Belong($__leveled_item_one__)){
                    $__leveled_item_one_____leveled_item_two__s = $__some__item_1__->__leveled_item_two__s()->where('__some__item__6__parent', $__leveled_item_one__->__some__item__6__id)/*->orderBy('__some__item__6__order', 'asc')*/->orderByRaw('-__some__item__6__order DESC')->orderBy('__some__item__6__id', 'asc')->get();
                }else{
                    continue;
                }
            }
            else{
                $__leveled_item_one_____leveled_item_two__s = $__leveled_item_one__->childs()->where('__some__item__6__offline', null)/*->orderBy('__some__item__6__order', 'asc')*/->orderByRaw('-__some__item__6__order DESC')->orderBy('__some__item__6__id', 'asc')->get();
            }


            foreach ($__leveled_item_one_____leveled_item_two__s as $__leveled_item_two__) {
                $__leveled_item_two___info['__some__item__6__id'] = $__leveled_item_two__->__some__item__6__id;

                $__leveled_item_two___info['__some__item__6__name'] = $__leveled_item_two__->__some__item__6__name;

                $__leveled_item_two___info['__some__item__6__display_name'] = $__leveled_item_two__->__some__item__6__display_name;

                $__leveled_item_two___info['__some__item__6__icon'] = $__leveled_item_two__->__some__item__6__icon;

                $query = __some_item_2__::where('__some_item_2_____some__item__6__id', $__leveled_item_two__->__some__item__6__id)
                    ->where('__some_item_2___is___some_item_4__', null)
                    ->where('__some_item_2___offline', null);


                if($option == 'wishlist' && $__some__item__5_!=null){
                    $query->whereRaw(
                        '__some_item_2__s.__some_item_2___id IN ( 
                            SELECT wish_fav___some_item_2___id FROM wish_fav WHERE wish_fav___some__item__5__id = ? AND wish_fav_type = ? AND deleted_at IS NULL
                        )',
                        [$__some__item__5_->__some__item__5__id, 'wish']
                    );
                }
                elseif($option == 'favorites' && $__some__item__5_!=null){
                    $query->whereRaw(
                        '__some_item_2__s.__some_item_2___id IN ( 
                            SELECT wish_fav___some_item_2___id FROM wish_fav WHERE wish_fav___some__item__5__id = ? AND wish_fav_type = ? AND deleted_at IS NULL
                        )',
                        [$__some__item__5_->__some__item__5__id, 'favorite']
                    );
                }



                foreach ((array) $__some_item_9__s as $__some_item_9__){

                    if ($__some_item_9__['checkbox_value'] == 'on') {
                        $id = $__some_item_9__['__some_item_9___id'];

                        $value = $__some_item_9__['value'];

                        $query->leftjoin("__some_item_2__s___some_item_9__s as __some_item_2__s___some_item_9__s_$id", '__some_item_2__s.__some_item_2___id', "__some_item_2__s___some_item_9__s_$id.__some_item_2_____some_item_9_____some_item_2___id")
                            ->where("__some_item_2__s___some_item_9__s_$id.__some_item_2_____some_item_9_____some_item_9___id", $id)
                            ->where("__some_item_2__s___some_item_9__s_$id.__some_item_2_____some_item_9___value", '<=', $value+$volume)
                            ->where("__some_item_2__s___some_item_9__s_$id.__some_item_2_____some_item_9___value", '>=', $value-$volume);
                    }

                }


                $__leveled_item_two___info['__some__item__6__number___some_item_2__s'] = $query->count();

                $number_of___leveled_item_one_____some_item_2__s = $number_of___leveled_item_one_____some_item_2__s + $__leveled_item_two___info['__some__item__6__number___some_item_2__s'];

                if($__leveled_item_two___info['__some__item__6__number___some_item_2__s']!=0){
                    $__leveled_item_two__s[] = $__leveled_item_two___info;
                }
            }

            if($number_of___leveled_item_one_____some_item_2__s!=0) {
                $data[] = [
                    '__some__item__6__id' => $__leveled_item_one__->__some__item__6__id,
                    '__some__item__6__name' => $__leveled_item_one__->__some__item__6__name,
                    '__some__item__6__display_name' => $__leveled_item_one__->__some__item__6__display_name,
                    '__some__item__6__icon' => $__leveled_item_one__->__some__item__6__icon,
                    '__some__item__6__number___some_item_2__s' => $number_of___leveled_item_one_____some_item_2__s,
                    '__some__item__6__childs' => $__leveled_item_two__s
                ];
            }
        }

        return $data;

    }


    public function __leveled_item_three__Info(__some__item__6_ $__leveled_item_three__, $__some_item_9__s, $volume, $__some__item_1__, $option, __some__item__5_ $__some__item__5_)
    {
        $data = [];

        foreach ($__leveled_item_three__->childs()
                     ->where('__some__item__6__offline', null)
                     ->orderByRaw('-__some__item__6__order DESC')
                     ->orderBy('__some__item__6__id', 'asc')
                     ->get() as $__leveled_item_one__
        ) {
            if($option == '__some_item_4__s'){
                $number_of___leveled_item_one_____some_item_2__s = __some_item_2__::where('__some_item_2_____some__item__6__id', $__leveled_item_one__->__some__item__6__id)
                    ->where('__some_item_2___is___some_item_4__', 1)
                    ->where('__some_item_2___offline', null)
                    ->where('__some_item_2___path', 'like', $__some__item__5_!=null ? $__some__item__5_->__some__item__5__id.'/%' : null)->count();

                if($number_of___leveled_item_one_____some_item_2__s!=0) {
                    $data[] = [
                        '__some__item__6__id' => $__leveled_item_one__->__some__item__6__id,
                        '__some__item__6__name' => $__leveled_item_one__->__some__item__6__name,
                        '__some__item__6__display_name' => $__leveled_item_one__->__some__item__6__display_name,
                        '__some__item__6__icon' => $__leveled_item_one__->__some__item__6__icon,
                        '__some__item__6__number___some_item_2__s' => $number_of___leveled_item_one_____some_item_2__s,
                        '__some__item__6__childs' => []
                    ];
                }
                continue;
            }

            $__leveled_item_two__s= [];

            $number_of___leveled_item_one_____some_item_2__s = 0;

            if($__some__item_1__!==null) {
                if($__some__item_1__->does__leveled_item_one__Belong($__leveled_item_one__)){
                    $__leveled_item_one_____leveled_item_two__s = $__some__item_1__->__leveled_item_two__s()
                        ->where('__some__item__6__parent', $__leveled_item_one__->__some__item__6__id)
                        ->orderByRaw('-__some__item__6__order DESC')
                        ->orderBy('__some__item__6__id', 'asc')
                        ->get();
                }else{
                    continue;
                }
            }
            else{
                $__leveled_item_one_____leveled_item_two__s = $__leveled_item_one__->childs()
                    ->where('__some__item__6__offline', null)
                    ->orderByRaw('-__some__item__6__order DESC')
                    ->orderBy('__some__item__6__id', 'asc')
                    ->get();
            }


            foreach ($__leveled_item_one_____leveled_item_two__s as $__leveled_item_two__) {
                $__leveled_item_two___info['__some__item__6__id'] = $__leveled_item_two__->__some__item__6__id;

                $__leveled_item_two___info['__some__item__6__name'] = $__leveled_item_two__->__some__item__6__name;

                $__leveled_item_two___info['__some__item__6__display_name'] = $__leveled_item_two__->__some__item__6__display_name;

                $__leveled_item_two___info['__some__item__6__icon'] = $__leveled_item_two__->__some__item__6__icon;

                $__leveled_item_two__s_ids=[$__leveled_item_two__->__some__item__6__id];

                $__leveled_item_two___info['__some__item__6__number___some_item_2__s'] = __some__item_1__::get__some_item_2__sCountFor__leveled_item_two__($option, $__some__item__5_, $__leveled_item_two__s_ids, $__some_item_9__s, $volume);

                $number_of___leveled_item_one_____some_item_2__s = $number_of___leveled_item_one_____some_item_2__s + $__leveled_item_two___info['__some__item__6__number___some_item_2__s'];

                if($__leveled_item_two___info['__some__item__6__number___some_item_2__s']!=0){
                    $__leveled_item_two__s[] = $__leveled_item_two___info;
                }
            }

            if($number_of___leveled_item_one_____some_item_2__s!=0) {
                $data[] = [
                    '__some__item__6__id' => $__leveled_item_one__->__some__item__6__id,
                    '__some__item__6__name' => $__leveled_item_one__->__some__item__6__name,
                    '__some__item__6__display_name' => $__leveled_item_one__->__some__item__6__display_name,
                    '__some__item__6__icon' => $__leveled_item_one__->__some__item__6__icon,
                    '__some__item__6__number___some_item_2__s' => $number_of___leveled_item_one_____some_item_2__s,
                    '__some__item__6__childs' => $__leveled_item_two__s
                ];
            }
        }

        return $data;

    }


    public function __leveled_item_three__InfoForBuilder(Request $request, __some__item__6_ $__leveled_item_three__, $volume, __some__item__5_ $__some__item__5_)
    {
        return $this->response($this->__leveled_item_three__Info($__leveled_item_three__, $request->data, $volume, null,'builder', $__some__item__5_));
    }


    public function __leveled_item_three__InfoForViewer(Request $request, __some__item_1__ $__some__item_1__, __some__item__6_ $__leveled_item_three__, $volume, __some__item__5_ $__some__item__5_)
    {
        $__some_item_9__s=[];

        foreach ($request->data as $__some_item_9__){
            $__some_item_9__s[] = [
                '__some_item_9___id'=>$__some_item_9__['__some_item_9__']['__some_item_9___id'],
                'checkbox_value'=>'on',
                'value' => $__some_item_9__['value']
            ];
        }

        return $this->response($this->__leveled_item_three__Info($__leveled_item_three__, $__some_item_9__s, $volume, $__some__item_1__, 'viewer', $__some__item__5_));
    }


    public function __leveled_item_three__InfoFor__some_item_2__List(Request $request, __some__item__6_ $__leveled_item_three__, __some__item__5_ $__some__item__5_)
    {
        return $this->response($this->__leveled_item_three__Info($__leveled_item_three__, null, null, null, $request->data, $__some__item__5_));
    }


    public function __some_item_2__sFrom__leveled_item_one____leveled_item_two__sOld(Request $request, $__some__item_1__, $volume, __some__item__5_ $__some__item__5_)
    {
        $__leveled_item_two__s=[];

        foreach($request->data['__leveled_item_two__s'] as $__leveled_item_two__){
            if($__leveled_item_two__['selected']) {
                $__leveled_item_two__s[] = $__leveled_item_two__['__some__item__6__id'];
            }
        }

        $__some_item_9__s = [];


        foreach( (array) $request->data['__some_item_9__s'] as $__some_item_9__){
            $__some_item_9__s[]=[$__some_item_9__['__some_item_9__']['__some_item_9___id'], $__some_item_9__['value']];
        }

        $hidden = $request->data['hidden'];

        $sorting = $request->data['sorting'];

        $sorting_map = [
            'relevance',
            'relevance',
            '__some_item_2_____some_item_8__',
            '__some_item_2___num_pages',
            '__some_item_2___hits',
            '__some_item_2_____some_item_7___count',
            '__some_item_2___favourite_count',
            '__some_item_2___extraction_hits',
            '__some_item_2___id',
            '__some_item_2___id'
        ];

        $__some_item_4__s = $request->data['__some_item_4__s'];

        $all___some_item_4__s = $request->data['all___some_item_4__s'];

        $pagination = $request->data['pagination'];

        $page = $request->data['page'];

        $option = $request->data['option'];

        $__leveled_item_one___id = $request->data['__leveled_item_one___id'];



        $query = __some_item_2__::whereIn('__some_item_2_____some__item__6__id', $__leveled_item_two__s)
            ->where('__some_item_2___offline', null)
            ->where('__some_item_2___is___some_item_4__', null);

        //----

        if($option == '__some_item_4__s'){
            $query = __some_item_2__::where('__some_item_2_____some__item__6__id', $__leveled_item_one___id)
                ->where('__some_item_2___is___some_item_4__', 1)
                ->where('__some_item_2___offline', null)
                ->where('__some_item_2___path', 'like', $__some__item__5_->__some__item__5__id.'/%');
        }

        if($option == 'wishlist'){
            $query->whereRaw(
                '__some_item_2__s.__some_item_2___id IN ( 
                            SELECT wish_fav___some_item_2___id FROM wish_fav WHERE wish_fav___some__item__5__id = ? AND wish_fav_type = ? AND deleted_at IS NULL
                        )',
                [$__some__item__5_->__some__item__5__id, 'wish']
            );
        }

        if($option == 'favorites'){
            $query->whereRaw(
                '__some_item_2__s.__some_item_2___id IN ( 
                            SELECT wish_fav___some_item_2___id FROM wish_fav WHERE wish_fav___some__item__5__id = ? AND wish_fav_type = ? AND deleted_at IS NULL
                        )',
                [$__some__item__5_->__some__item__5__id, 'favorite']
            );
        }


        foreach ((array) $__some_item_9__s as $__some_item_9__){
            $query->leftjoin("__some_item_2__s___some_item_9__s as __some_item_2__s___some_item_9__s_$__some_item_9__[0]", '__some_item_2__s.__some_item_2___id', "__some_item_2__s___some_item_9__s_$__some_item_9__[0].__some_item_2_____some_item_9_____some_item_2___id")
                ->where("__some_item_2__s___some_item_9__s_$__some_item_9__[0].__some_item_2_____some_item_9_____some_item_9___id", $__some_item_9__[0])
                ->where("__some_item_2__s___some_item_9__s_$__some_item_9__[0].__some_item_2_____some_item_9___value", '<=', $__some_item_9__[1]+$volume)
                ->where("__some_item_2__s___some_item_9__s_$__some_item_9__[0].__some_item_2_____some_item_9___value", '>=', $__some_item_9__[1]-$volume);

        }

        if(!$hidden && $__some__item_1__!=null){

            // Important: do not to forget about deleted_at - these values are ignored without direct using
            $query->whereRaw(
                    '__some_item_2__s.__some_item_2___id NOT IN ( 
                        SELECT hide___some_item_2___id FROM hides WHERE hide___some__item__5__id = ? AND hide___some__item_1___id = ? AND deleted_at IS NULL
                    )',
                [$__some__item__5_->__some__item__5__id, $__some__item_1__->__some__item_1___id]
            );
        }

        if($__some__item_1__!=null && !in_array($__some__item__5_->__some__item__5__id, Config::get('__project__config.default___some__item__5_s_ids'))){
            foreach (Config::get('__project__config.default___some__item__5_s_ids') as $default___some__item__5__id){
                $query->whereRaw(
                    '__some_item_2__s.__some_item_2___id NOT IN ( 
                        SELECT hide___some_item_2___id FROM hides WHERE hide___some__item__5__id = ? AND hide___some__item_1___id = ? AND deleted_at IS NULL
                    )',
                    [$default___some__item__5__id, $__some__item_1__->__some__item_1___id]
                );
            }
        }


        if($__some_item_4__s && $__some__item_1__!=null){

            if($all___some_item_4__s){
                $__some_item_4__s_query = __some_item_2__::where('__some_item_2___path', 'like', $__some__item__5_->__some__item__5__id.'/__some_item_4__s/%')
                    ->where('__some_item_2___is___some_item_4__', '1')
                    ->where('__some_item_2___offline', null);
            }
            else{
                $__some_item_4__s_query = __some_item_2__::where('__some_item_2___path', 'like', $__some__item__5_->__some__item__5__id.'/__some_item_4__s/'.$__some__item_1__->__some__item_1___id)
                    ->where('__some_item_2___is___some_item_4__', '1')
                    ->where('__some_item_2___offline', null)
                    ->where('__some_item_2_____some__item__6__id', $__leveled_item_one___id);
            }

            $query->union($__some_item_4__s_query)->orderBy('__some_item_2___is___some_item_4__', 'desc');

        }

        if($sorting_map[$sorting]!='relevance'){
            $query->orderBy($sorting_map[$sorting], 'desc');
        }
        else{
            $query->orderBy('__some_item_2_____some_item_8__', 'desc'); //RELEVANCE COLUMN NEEDED
        }


        $query = $query->select('__some_item_2__s.*')
            ->with(array(
                'parent__some__item__6_'=>function($query){
                    $query->select('__some__item__6__id', '__some__item__6__name', '__some__item__6__display_name');
                }));


        $__some_item_2__s = $query->skip($pagination*($page-1))
            ->take($pagination+1)
            ->get();

        $response =[];

        $end_of_list = true;

        if($__some_item_2__s->count()>$pagination){
            $__some_item_2__s->pop();
            $end_of_list = false;
        }

        foreach ($__some_item_2__s as $__some_item_2__){

            if($__some_item_2__->__some_item_2___is___some_item_4__){
                $meta=[
                    'is_favorite'=>false,
                    'is_wish'=>false,
                    'is_hidden'=>false,
                    '__some_item_2_____some__item__5____some_item_8__'=>null,
                    '__some_item_2_____some__item__5____some_item_7__'=>null,
                    '__some_item_2___last_editor___some_item_8__'=>null,
                    '__some_item_9__s'=>null
                ];
            }
            else{
                $meta=[
                    'is_favorite'=>$__some_item_2__->isFavorite($__some__item__5_),
                    'is_wish'=>$__some_item_2__->isWish($__some__item__5_),
                    'is_hidden'=>$__some__item_1__==null ? false : $__some_item_2__->isHidden($__some__item_1__, $__some__item__5_),
                    '__some_item_2_____some__item__5____some_item_8__'=>$__some_item_2__->__some__item__5___some_item_8__($__some__item__5_) == null ? null : $__some_item_2__->__some__item__5___some_item_8__($__some__item__5_)->__some_item_8_____some_item_8__,
                    '__some_item_2_____some__item__5____some_item_7__'=>$__some_item_2__->__some__item__5___some_item_7__($__some__item__5_) == null ? null : $__some_item_2__->__some__item__5___some_item_7__($__some__item__5_)->__some_item_7___content,
                    '__some_item_2___last_editor___some_item_8__'=>$__some_item_2__->lastEditor__some_item_8__() == null ? null : $__some_item_2__->lastEditor__some_item_8__()->__some_item_8_____some_item_8__,
                    '__some_item_2_____some_item_9__s'=>$__some_item_2__->__some_item_9__s()->get()
                ];
            }

            $response[] = ['__some_item_2__' => $__some_item_2__, 'meta'=>$meta];
        }

        return $this->response(['__some_item_2__s' => $response, 'end_of_list' => $end_of_list]);

    }

    public function __some_item_2__sFrom__leveled_item_one____leveled_item_two__s(Request $request, $__some__item_1__, $volume, __some__item__5_ $__some__item__5_)
    {
        $__leveled_item_two__s=[];

        foreach($request->data['__leveled_item_two__s'] as $__leveled_item_two__){
            if($__leveled_item_two__['selected']) {
                $__leveled_item_two__s[] = $__leveled_item_two__['__some__item__6__id'];
            }
        }

        $__some_item_9__s = [];


        foreach( (array) $request->data['__some_item_9__s'] as $__some_item_9__){
            $__some_item_9__s[]=[
                '__some_item_9___id' => $__some_item_9__['__some_item_9__']['__some_item_9___id'],
                'value'=>$__some_item_9__['value'],
                'checkbox_value'=>'on'
            ];
        }

        $hidden = $request->data['hidden'];

        $sorting = $request->data['sorting'];

        $sorting_map = [
            'relevance',
            'relevance',
            '__some_item_2_____some_item_8__',
            '__some_item_2___num_pages',
            '__some_item_2___hits',
            '__some_item_2_____some_item_7___count',
            '__some_item_2___favourite_count',
            '__some_item_2___extraction_hits',
            '__some_item_2___id',
            '__some_item_2___id'
        ];

        $__some_item_4__s = $request->data['__some_item_4__s'];

        $all___some_item_4__s = $request->data['all___some_item_4__s'];

        $pagination = $request->data['pagination'];

        $page = $request->data['page'];

        $option = $request->data['option'];

        $__leveled_item_one___id = $request->data['__leveled_item_one___id'];


        $response =[];

        $sorted___some_item_2__s = __some__item_1__::get__some_item_2__sFor__leveled_item_two__s($option, $__some__item__5_, $__some__item_1__, $hidden,
            $__some_item_4__s, $all___some_item_4__s, $__leveled_item_one___id, $__leveled_item_two__s,
            $__some_item_9__s, $volume, $pagination, $page,
            $sorting, $sorting_map);


        foreach ($sorted___some_item_2__s['__some_item_2__s'] as $__some_item_2__){

            if($__some_item_2__->__some_item_2___is___some_item_4__){
                $meta=[
                    'is_favorite'=>false,
                    'is_wish'=>false,
                    'is_hidden'=>false,
                    '__some_item_2_____some__item__5____some_item_8__'=>null,
                    '__some_item_2_____some__item__5____some_item_7__'=>null,
                    '__some_item_2___last_editor___some_item_8__'=>null,
                    '__some_item_9__s'=>null,
                    'related___some__item_1__'=> $__some_item_2__->related__some__item_1__()
                ];
            }
            else{
                $meta=[
                    'is_favorite'=>$__some_item_2__->isFavorite($__some__item__5_),
                    'is_wish'=>$__some_item_2__->isWish($__some__item__5_),
                    'is_hidden'=>$__some__item_1__==null ? false : $__some_item_2__->isHidden($__some__item_1__, $__some__item__5_),
                    '__some_item_2_____some__item__5____some_item_8__'=>$__some_item_2__->__some__item__5___some_item_8__($__some__item__5_) == null ? null : $__some_item_2__->__some__item__5___some_item_8__($__some__item__5_)->__some_item_8_____some_item_8__,
                    '__some_item_2_____some__item__5____some_item_7__'=>$__some_item_2__->__some__item__5___some_item_7__($__some__item__5_) == null ? null : $__some_item_2__->__some__item__5___some_item_7__($__some__item__5_)->__some_item_7___content,
                    '__some_item_2___last_editor___some_item_8__'=>$__some_item_2__->lastEditor__some_item_8__() == null ? null : $__some_item_2__->lastEditor__some_item_8__()->__some_item_8_____some_item_8__,
                    '__some_item_2_____some_item_9__s'=>$__some_item_2__->__some_item_9__s()->get(),
                    'related___some__item_1__'=> null
                ];
            }

            $response[] = ['__some_item_2__' => $__some_item_2__, 'meta'=>$meta];
        }

        return $this->response(['__some_item_2__s' => $response, 'end_of_list' => $sorted___some_item_2__s['end_of_list']]);

    }


    public function __some_item_2__sFrom__leveled_item_one____leveled_item_two__sNo__some__item_1__(Request $request, __some__item__5_ $__some__item__5_)
    {
        return $this->__some_item_2__sFrom__leveled_item_one____leveled_item_two__s($request, null, null, $__some__item__5_);
    }


    public function rate__some__item_1__(Request $request, __some__item_1__ $__some__item_1__, __some__item__5_ $__some__item__5_)
    {
        $__some__item_1__->rate($__some__item__5_, $request->data['__some_item_8__']);

        return $this->response([
            '__some__item_1___average___some_item_8__'=>$__some__item_1__->average__some_item_8__(),
            '__some__item_1___number_votes' => $__some__item_1__->__some_item_8__s()->count()
        ]);
    }


    public function __some_item_7____some__item_1__(Request $request, __some__item_1__ $__some__item_1__, __some__item__5_ $__some__item__5_)
    {
        $__some__item_1__->__some_item_7__($__some__item__5_, $request->data['__some_item_7__']);

        return $this->response('__some_item_7__ed');
    }


    public function img(__some__item_1__ $__some__item_1__)
    {
        return $__some__item_1__->img($__some__item_1__);
    }

    public function __some_item_7__s(__some__item_1__ $__some__item_1__, __some__item__5_ $__some__item__5_)
    {
        return $this->response($__some__item_1__->approved__some_item_7__s()->get());
    }


    public function delete__some__item_1__(__some__item_1__ $__some__item_1__, __some__item__5_ $__some__item__5_)
    {
        $__some__item_1__->remove__some__item_1__($__some__item__5_);
        return $this->response(true);
    }

    public function uploadRandom__some__item_1__s(Request $request, __some__item__5_ $__some__item__5_)
    {
        $__some__item_1__s_to_take = Config::get('__project__config.random___some__item_1__s_count');

        $__some__item_1__s=[];

        if(!isset($request->data['option'])){
            return response('Not found', 404);
        }
        elseif($request->data['option'] == 'default'){
            $__some__item_1__s = __some__item_1__::with('group')
                ->whereIn('__some__item_1___owner_id', Config::get('__project__config.default___some__item__5_s_ids'))
                ->inRandomOrder()
                ->take($__some__item_1__s_to_take)
                ->get();
        }
        elseif($request->data['option'] == '__some__item__5_'){
            $__some__item_1__s = __some__item_1__::with('group')
                ->where('__some__item_1___owner_id', $__some__item__5_->__some__item__5__id)
                ->inRandomOrder()
                ->take($__some__item_1__s_to_take)
                ->get();
        }
        else{
            return response('Not found', 404);
        }

        $response=[];

        foreach ($__some__item_1__s as $__some__item_1__){

            $response[]=[
                '__some__item_1__'=>$__some__item_1__,
                '__some__item_1___average___some_item_8__'=> $__some__item_1__->average__some_item_8__(),
            ];
        }

        return $this->response($response);
    }


}
