<?php

namespace App\Http\Controllers;

use App\SomeItem2;
use App\SomeItem6;
use App\SomeItem7;
use App\SomeItem1;
use App\SomeItem1sGroup;
use App\SomeItem5;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Imagick;
use PDO;
use Project\Facades\SomeAuthService;
use Project\Facades\ProjectSync;

class SomeItem1Controller extends Controller
{
    public function builderSteps(SomeItem5 $SomeItem5)
    {
        $SomeItem6s = SomeItem6::where('SomeItem6_type', 'LeveledItemThree')->where('SomeItem6_offline', null)/*->orderBy('SomeItem6_order', 'asc')*/->orderByRaw('-SomeItem6_order DESC')->orderBy('SomeItem6_id', 'asc')->get();

        $data = [['type'=>'SomeItem9s', 'url'=>'SomeItem9s', 'number'=>1, 'name'=>'SomeItem9s']];

        // TODO it is the mock, than it will be defined by SomeItem6_number field
        $i = 1;

        foreach($SomeItem6s as $SomeItem6){
            $data[]=[
                'type'=>'LeveledItemThree',
                'id'=>$SomeItem6->SomeItem6_id,
                'number'=>++$i,
                'name'=>empty($SomeItem6->SomeItem6_display_name)
                    ? ucwords(str_replace('_', ' ', $SomeItem6->SomeItem6_name), ' ')
                    : $SomeItem6->SomeItem6_display_name
            ];
        }

        return $this->response($data);
    }


    public function uploadImgTmp(Request $request, SomeItem5 $SomeItem5)
    {
        $SomeItem2 = SomeItem2_get_contents($request->SomeItem2('SomeItem2')->getRealPath());
        $original_SomeItem2_name = $request->SomeItem2('SomeItem2')->getClientOriginalName();

        $disk = Storage::disk(Config::get('Projectconfig.Project_tmp_disk'));

        $timestamp=date('YmdHis');

        $SomeItem2_name = $original_SomeItem2_name.'_'.$timestamp.'_'.SomeAuthService::id();

        $disk->put($SomeItem2_name, $SomeItem2);

        return $this->response($SomeItem2_name);
    }


    public function save(Request $request)
    {
        $SomeItem1 = null;

        $data = $request->data;

        foreach ($data as $key => $item) {
            if($key=='0'){
                $SomeItem1 = SomeItem1::buildOrUpdate($item);
            }
            else{
                $SomeItem1->attachLeveledItemTwos($item);
            }
        }

        return $this->response($SomeItem1->SomeItem1_id);
    }


    public function SomeItem1(SomeItem1 $SomeItem1, $SomeItem5)
    {
        $group =$SomeItem1->group()->first();

        $customized_SomeItem1s = SomeItem1::whereRaw('SomeItem1_name RLIKE CONCAT(?, "[0-9]*")', [$SomeItem1->SomeItem1_name.' - Customized '])
            ->where('SomeItem1_owner_id', $SomeItem5->SomeItem5_id);

        if($customized_SomeItem1s->first() == null){

            if (SomeItem1::where('SomeItem1_name', $SomeItem1->SomeItem1_name.' - Customized')
                    ->where('SomeItem1_owner_id', $SomeItem5->SomeItem5_id)->first()!=null
            ){
                $default_name = $SomeItem1->SomeItem1_name.' - Customized 2';
            }
            else{
                $default_name = $SomeItem1->SomeItem1_name.' - Customized';
            }
        }
        else{
            $default_name = $customized_SomeItem1s->orderBy('SomeItem1_name', 'desc')->first()->SomeItem1_name;

            $number = (int) substr($default_name, strripos($default_name, ' '));

            $number++;

            $default_name = substr($default_name, 0, strripos($default_name, ' ')+1).$number;

        }

        $pre_SomeItem9s = [];

        $SomeItem9s = $SomeItem1->SomeItem9s()->get();

        foreach ($SomeItem9s as $SomeItem9){
            $pre_SomeItem9s['SomeItem9_'.$SomeItem9->SomeItem9_id]=[
                'id' => $SomeItem9->SomeItem9_id,
                'value' => $SomeItem9->pivot->SomeItem1_meta_SomeItem9_value
            ];
        }

        $default_state = [
            'SomeItem9s_step'=>[
                'name'=>$default_name,
                'description'=>$SomeItem1->SomeItem1_description,
                'group'=>[
                    'id'=>$group == null ? null : $group->SomeItem1_group_id,
                    'name'=>$group == null ? null : $group->SomeItem1_group_display_name
                ],
                'pre_SomeItem9s' => $pre_SomeItem9s,
                'SomeItem1_id' =>$SomeItem1->SomeItem1_id,
                'SomeItem1_initial_name' => $SomeItem1->SomeItem1_name,
                'SomeItem1_default_icon' => $SomeItem1->SomeItem1_icon
            ]
        ];


        foreach($SomeItem1->LeveledItemTwos()->with('parent')->with('parent.parent')->get() as $LeveledItemTwo){
            $default_state['LeveledItemThree_'.$LeveledItemTwo->parent->parent->SomeItem6_id]['LeveledItemOne_'.$LeveledItemTwo->parent->SomeItem6_id][]
                = $LeveledItemTwo->SomeItem6_id;
        }

        foreach ($SomeItem1->LeveledItemThrees()->get() as $LeveledItemThree){
            $default_state['LeveledItemThree_'.$LeveledItemThree->SomeItem6_id]['volume_slider'] = $LeveledItemThree->pivot->SomeItem1_meta_results_volume;
        }


        return $this->response([
            'SomeItem1'=>$SomeItem1,
            'group'=>$group,
            'SomeItem1_LeveledItemThrees'=>$SomeItem1->LeveledItemThrees()->withCount('childs')->orderByRaw('-SomeItem6_order DESC')->orderBy('SomeItem6_id', 'asc')->get(),
            'SomeItem1_SomeItem9s'=> $SomeItem9s,
            'SomeItem1_average_SomeItem8'=>$SomeItem1->averageSomeItem8(),
            'SomeItem1_number_votes'=>$SomeItem1->SomeItem8s()->count(),
            'SomeItem1_number_SomeItem7s'=>$SomeItem1->approvedSomeItem7s()->count(),
            'SomeItem1_SomeItem5_SomeItem8' => $SomeItem1->SomeItem5SomeItem8($SomeItem5)==null ? null : $SomeItem1->SomeItem5SomeItem8($SomeItem5)->SomeItem8_SomeItem8,
            'SomeItem1_SomeItem5_SomeItem7' => $SomeItem1->SomeItem5SomeItem7($SomeItem5)==null ? null : $SomeItem1->SomeItem5SomeItem7($SomeItem5)->SomeItem7_content,
            'SomeItem1_default_state' => $default_state,
            'SomeItem1_is_owner' => $SomeItem1->SomeItem1_owner_id == $SomeItem5->SomeItem5_id || in_array($SomeItem5->SomeItem5_id, Config::get('Projectconfig.default_SomeItem5s_ids')) ? true : false,
            'SomeItem1_number_SomeItem2s' => $SomeItem1->SomeItem2sCount(),
            'SomeItem1_number_LeveledItemTwos' => $SomeItem1->LeveledItemTwosCount(),
            'SomeItem1_number_LeveledItemOnes' => $SomeItem1->LeveledItemOnesCount(),
            'SomeItem1_number_LeveledItemThrees' => $SomeItem1->LeveledItemThreesCount(),
        ]);
    }

    public function SomeItem1s(Request $request, SomeItem5 $SomeItem5)
    {
        $type = $request->data['type'];
        $group_id = $request->data['group_id'];
        $sorting_option = $request->data['sorting_option'];
        $page = $request->data['page'];
        $pagination = $request->data['pagination'];

        $sorting_map = [
            'default',
            'relevance',
            'LeveledItemThrees_number',
            'LeveledItemOnes_number',
            'LeveledItemTwos_number',
            'SomeItem2s_number',
            'average_SomeItem8',
            'SomeItem7s_number'
        ];

        $sorting_option = isset($sorting_map[$sorting_option]) ? $sorting_map[$sorting_option] : 'default';

        $list = SomeItem1::sortAndPaginate($type, $sorting_option, $group_id, $page, $pagination, $SomeItem5);

        return $this->response($list);
    }


    // Option defines which part of frontend calls this method
    public function LeveledItemThreeInfoOld(SomeItem6 $LeveledItemThree, $SomeItem9s, $volume, $SomeItem1, $option, SomeItem5 $SomeItem5)
    {
        $data = [];

        foreach ($LeveledItemThree->childs()->where('SomeItem6_offline', null)/*->orderBy('SomeItem6_order', 'asc')*/->orderByRaw('-SomeItem6_order DESC')->orderBy('SomeItem6_id', 'asc')->get() as $LeveledItemOne) {

            if($option == 'SomeItem4s'){
                $number_of_LeveledItemOne_SomeItem2s = SomeItem2::where('SomeItem2_SomeItem6_id', $LeveledItemOne->SomeItem6_id)
                    ->where('SomeItem2_is_SomeItem4', 1)
                    ->where('SomeItem2_offline', null)
                    ->where('SomeItem2_path', 'like', $SomeItem5!=null ? $SomeItem5->SomeItem5_id.'/%' : null)->count();

                if($number_of_LeveledItemOne_SomeItem2s!=0) {
                    $data[] = [
                        'SomeItem6_id' => $LeveledItemOne->SomeItem6_id,
                        'SomeItem6_name' => $LeveledItemOne->SomeItem6_name,
                        'SomeItem6_display_name' => $LeveledItemOne->SomeItem6_display_name,
                        'SomeItem6_icon' => $LeveledItemOne->SomeItem6_icon,
                        'SomeItem6_number_SomeItem2s' => $number_of_LeveledItemOne_SomeItem2s,
                        'SomeItem6_childs' => []
                    ];
                }
                continue;
            }



            $LeveledItemTwos= [];

            $number_of_LeveledItemOne_SomeItem2s = 0;

            if($SomeItem1!==null) {
                if($SomeItem1->doesLeveledItemOneBelong($LeveledItemOne)){
                    $LeveledItemOne_LeveledItemTwos = $SomeItem1->LeveledItemTwos()->where('SomeItem6_parent', $LeveledItemOne->SomeItem6_id)/*->orderBy('SomeItem6_order', 'asc')*/->orderByRaw('-SomeItem6_order DESC')->orderBy('SomeItem6_id', 'asc')->get();
                }else{
                    continue;
                }
            }
            else{
                $LeveledItemOne_LeveledItemTwos = $LeveledItemOne->childs()->where('SomeItem6_offline', null)/*->orderBy('SomeItem6_order', 'asc')*/->orderByRaw('-SomeItem6_order DESC')->orderBy('SomeItem6_id', 'asc')->get();
            }


            foreach ($LeveledItemOne_LeveledItemTwos as $LeveledItemTwo) {
                $LeveledItemTwo_info['SomeItem6_id'] = $LeveledItemTwo->SomeItem6_id;

                $LeveledItemTwo_info['SomeItem6_name'] = $LeveledItemTwo->SomeItem6_name;

                $LeveledItemTwo_info['SomeItem6_display_name'] = $LeveledItemTwo->SomeItem6_display_name;

                $LeveledItemTwo_info['SomeItem6_icon'] = $LeveledItemTwo->SomeItem6_icon;

                $query = SomeItem2::where('SomeItem2_SomeItem6_id', $LeveledItemTwo->SomeItem6_id)
                    ->where('SomeItem2_is_SomeItem4', null)
                    ->where('SomeItem2_offline', null);


                if($option == 'wishlist' && $SomeItem5!=null){
                    $query->whereRaw(
                        'SomeItem2s.SomeItem2_id IN ( 
                            SELECT wish_fav_SomeItem2_id FROM wish_fav WHERE wish_fav_SomeItem5_id = ? AND wish_fav_type = ? AND deleted_at IS NULL
                        )',
                        [$SomeItem5->SomeItem5_id, 'wish']
                    );
                }
                elseif($option == 'favorites' && $SomeItem5!=null){
                    $query->whereRaw(
                        'SomeItem2s.SomeItem2_id IN ( 
                            SELECT wish_fav_SomeItem2_id FROM wish_fav WHERE wish_fav_SomeItem5_id = ? AND wish_fav_type = ? AND deleted_at IS NULL
                        )',
                        [$SomeItem5->SomeItem5_id, 'favorite']
                    );
                }



                foreach ((array) $SomeItem9s as $SomeItem9){

                    if ($SomeItem9['checkbox_value'] == 'on') {
                        $id = $SomeItem9['SomeItem9_id'];

                        $value = $SomeItem9['value'];

                        $query->leftjoin("SomeItem2s_SomeItem9s as SomeItem2s_SomeItem9s_$id", 'SomeItem2s.SomeItem2_id', "SomeItem2s_SomeItem9s_$id.SomeItem2_SomeItem9_SomeItem2_id")
                            ->where("SomeItem2s_SomeItem9s_$id.SomeItem2_SomeItem9_SomeItem9_id", $id)
                            ->where("SomeItem2s_SomeItem9s_$id.SomeItem2_SomeItem9_value", '<=', $value+$volume)
                            ->where("SomeItem2s_SomeItem9s_$id.SomeItem2_SomeItem9_value", '>=', $value-$volume);
                    }

                }


                $LeveledItemTwo_info['SomeItem6_number_SomeItem2s'] = $query->count();

                $number_of_LeveledItemOne_SomeItem2s = $number_of_LeveledItemOne_SomeItem2s + $LeveledItemTwo_info['SomeItem6_number_SomeItem2s'];

                if($LeveledItemTwo_info['SomeItem6_number_SomeItem2s']!=0){
                    $LeveledItemTwos[] = $LeveledItemTwo_info;
                }
            }

            if($number_of_LeveledItemOne_SomeItem2s!=0) {
                $data[] = [
                    'SomeItem6_id' => $LeveledItemOne->SomeItem6_id,
                    'SomeItem6_name' => $LeveledItemOne->SomeItem6_name,
                    'SomeItem6_display_name' => $LeveledItemOne->SomeItem6_display_name,
                    'SomeItem6_icon' => $LeveledItemOne->SomeItem6_icon,
                    'SomeItem6_number_SomeItem2s' => $number_of_LeveledItemOne_SomeItem2s,
                    'SomeItem6_childs' => $LeveledItemTwos
                ];
            }
        }

        return $data;

    }


    public function LeveledItemThreeInfo(SomeItem6 $LeveledItemThree, $SomeItem9s, $volume, $SomeItem1, $option, SomeItem5 $SomeItem5)
    {
        $data = [];

        foreach ($LeveledItemThree->childs()
                     ->where('SomeItem6_offline', null)
                     ->orderByRaw('-SomeItem6_order DESC')
                     ->orderBy('SomeItem6_id', 'asc')
                     ->get() as $LeveledItemOne
        ) {
            if($option == 'SomeItem4s'){
                $number_of_LeveledItemOne_SomeItem2s = SomeItem2::where('SomeItem2_SomeItem6_id', $LeveledItemOne->SomeItem6_id)
                    ->where('SomeItem2_is_SomeItem4', 1)
                    ->where('SomeItem2_offline', null)
                    ->where('SomeItem2_path', 'like', $SomeItem5!=null ? $SomeItem5->SomeItem5_id.'/%' : null)->count();

                if($number_of_LeveledItemOne_SomeItem2s!=0) {
                    $data[] = [
                        'SomeItem6_id' => $LeveledItemOne->SomeItem6_id,
                        'SomeItem6_name' => $LeveledItemOne->SomeItem6_name,
                        'SomeItem6_display_name' => $LeveledItemOne->SomeItem6_display_name,
                        'SomeItem6_icon' => $LeveledItemOne->SomeItem6_icon,
                        'SomeItem6_number_SomeItem2s' => $number_of_LeveledItemOne_SomeItem2s,
                        'SomeItem6_childs' => []
                    ];
                }
                continue;
            }

            $LeveledItemTwos= [];

            $number_of_LeveledItemOne_SomeItem2s = 0;

            if($SomeItem1!==null) {
                if($SomeItem1->doesLeveledItemOneBelong($LeveledItemOne)){
                    $LeveledItemOne_LeveledItemTwos = $SomeItem1->LeveledItemTwos()
                        ->where('SomeItem6_parent', $LeveledItemOne->SomeItem6_id)
                        ->orderByRaw('-SomeItem6_order DESC')
                        ->orderBy('SomeItem6_id', 'asc')
                        ->get();
                }else{
                    continue;
                }
            }
            else{
                $LeveledItemOne_LeveledItemTwos = $LeveledItemOne->childs()
                    ->where('SomeItem6_offline', null)
                    ->orderByRaw('-SomeItem6_order DESC')
                    ->orderBy('SomeItem6_id', 'asc')
                    ->get();
            }


            foreach ($LeveledItemOne_LeveledItemTwos as $LeveledItemTwo) {
                $LeveledItemTwo_info['SomeItem6_id'] = $LeveledItemTwo->SomeItem6_id;

                $LeveledItemTwo_info['SomeItem6_name'] = $LeveledItemTwo->SomeItem6_name;

                $LeveledItemTwo_info['SomeItem6_display_name'] = $LeveledItemTwo->SomeItem6_display_name;

                $LeveledItemTwo_info['SomeItem6_icon'] = $LeveledItemTwo->SomeItem6_icon;

                $LeveledItemTwos_ids=[$LeveledItemTwo->SomeItem6_id];

                $LeveledItemTwo_info['SomeItem6_number_SomeItem2s'] = SomeItem1::getSomeItem2sCountForLeveledItemTwo($option, $SomeItem5, $LeveledItemTwos_ids, $SomeItem9s, $volume);

                $number_of_LeveledItemOne_SomeItem2s = $number_of_LeveledItemOne_SomeItem2s + $LeveledItemTwo_info['SomeItem6_number_SomeItem2s'];

                if($LeveledItemTwo_info['SomeItem6_number_SomeItem2s']!=0){
                    $LeveledItemTwos[] = $LeveledItemTwo_info;
                }
            }

            if($number_of_LeveledItemOne_SomeItem2s!=0) {
                $data[] = [
                    'SomeItem6_id' => $LeveledItemOne->SomeItem6_id,
                    'SomeItem6_name' => $LeveledItemOne->SomeItem6_name,
                    'SomeItem6_display_name' => $LeveledItemOne->SomeItem6_display_name,
                    'SomeItem6_icon' => $LeveledItemOne->SomeItem6_icon,
                    'SomeItem6_number_SomeItem2s' => $number_of_LeveledItemOne_SomeItem2s,
                    'SomeItem6_childs' => $LeveledItemTwos
                ];
            }
        }

        return $data;

    }


    public function LeveledItemThreeInfoForBuilder(Request $request, SomeItem6 $LeveledItemThree, $volume, SomeItem5 $SomeItem5)
    {
        return $this->response($this->LeveledItemThreeInfo($LeveledItemThree, $request->data, $volume, null,'builder', $SomeItem5));
    }


    public function LeveledItemThreeInfoForViewer(Request $request, SomeItem1 $SomeItem1, SomeItem6 $LeveledItemThree, $volume, SomeItem5 $SomeItem5)
    {
        $SomeItem9s=[];

        foreach ($request->data as $SomeItem9){
            $SomeItem9s[] = [
                'SomeItem9_id'=>$SomeItem9['SomeItem9']['SomeItem9_id'],
                'checkbox_value'=>'on',
                'value' => $SomeItem9['value']
            ];
        }

        return $this->response($this->LeveledItemThreeInfo($LeveledItemThree, $SomeItem9s, $volume, $SomeItem1, 'viewer', $SomeItem5));
    }


    public function LeveledItemThreeInfoForSomeItem2List(Request $request, SomeItem6 $LeveledItemThree, SomeItem5 $SomeItem5)
    {
        return $this->response($this->LeveledItemThreeInfo($LeveledItemThree, null, null, null, $request->data, $SomeItem5));
    }


    public function SomeItem2sFromLeveledItemOneLeveledItemTwosOld(Request $request, $SomeItem1, $volume, SomeItem5 $SomeItem5)
    {
        $LeveledItemTwos=[];

        foreach($request->data['LeveledItemTwos'] as $LeveledItemTwo){
            if($LeveledItemTwo['selected']) {
                $LeveledItemTwos[] = $LeveledItemTwo['SomeItem6_id'];
            }
        }

        $SomeItem9s = [];


        foreach( (array) $request->data['SomeItem9s'] as $SomeItem9){
            $SomeItem9s[]=[$SomeItem9['SomeItem9']['SomeItem9_id'], $SomeItem9['value']];
        }

        $hidden = $request->data['hidden'];

        $sorting = $request->data['sorting'];

        $sorting_map = [
            'relevance',
            'relevance',
            'SomeItem2_SomeItem8',
            'SomeItem2_num_pages',
            'SomeItem2_hits',
            'SomeItem2_SomeItem7_count',
            'SomeItem2_favourite_count',
            'SomeItem2_extraction_hits',
            'SomeItem2_id',
            'SomeItem2_id'
        ];

        $SomeItem4s = $request->data['SomeItem4s'];

        $all_SomeItem4s = $request->data['all_SomeItem4s'];

        $pagination = $request->data['pagination'];

        $page = $request->data['page'];

        $option = $request->data['option'];

        $LeveledItemOne_id = $request->data['LeveledItemOne_id'];



        $query = SomeItem2::whereIn('SomeItem2_SomeItem6_id', $LeveledItemTwos)
            ->where('SomeItem2_offline', null)
            ->where('SomeItem2_is_SomeItem4', null);

        //----

        if($option == 'SomeItem4s'){
            $query = SomeItem2::where('SomeItem2_SomeItem6_id', $LeveledItemOne_id)
                ->where('SomeItem2_is_SomeItem4', 1)
                ->where('SomeItem2_offline', null)
                ->where('SomeItem2_path', 'like', $SomeItem5->SomeItem5_id.'/%');
        }

        if($option == 'wishlist'){
            $query->whereRaw(
                'SomeItem2s.SomeItem2_id IN ( 
                            SELECT wish_fav_SomeItem2_id FROM wish_fav WHERE wish_fav_SomeItem5_id = ? AND wish_fav_type = ? AND deleted_at IS NULL
                        )',
                [$SomeItem5->SomeItem5_id, 'wish']
            );
        }

        if($option == 'favorites'){
            $query->whereRaw(
                'SomeItem2s.SomeItem2_id IN ( 
                            SELECT wish_fav_SomeItem2_id FROM wish_fav WHERE wish_fav_SomeItem5_id = ? AND wish_fav_type = ? AND deleted_at IS NULL
                        )',
                [$SomeItem5->SomeItem5_id, 'favorite']
            );
        }


        foreach ((array) $SomeItem9s as $SomeItem9){
            $query->leftjoin("SomeItem2s_SomeItem9s as SomeItem2s_SomeItem9s_$SomeItem9[0]", 'SomeItem2s.SomeItem2_id', "SomeItem2s_SomeItem9s_$SomeItem9[0].SomeItem2_SomeItem9_SomeItem2_id")
                ->where("SomeItem2s_SomeItem9s_$SomeItem9[0].SomeItem2_SomeItem9_SomeItem9_id", $SomeItem9[0])
                ->where("SomeItem2s_SomeItem9s_$SomeItem9[0].SomeItem2_SomeItem9_value", '<=', $SomeItem9[1]+$volume)
                ->where("SomeItem2s_SomeItem9s_$SomeItem9[0].SomeItem2_SomeItem9_value", '>=', $SomeItem9[1]-$volume);

        }

        if(!$hidden && $SomeItem1!=null){

            // Important: do not to forget about deleted_at - these values are ignored without direct using
            $query->whereRaw(
                    'SomeItem2s.SomeItem2_id NOT IN ( 
                        SELECT hide_SomeItem2_id FROM hides WHERE hide_SomeItem5_id = ? AND hide_SomeItem1_id = ? AND deleted_at IS NULL
                    )',
                [$SomeItem5->SomeItem5_id, $SomeItem1->SomeItem1_id]
            );
        }

        if($SomeItem1!=null && !in_array($SomeItem5->SomeItem5_id, Config::get('Projectconfig.default_SomeItem5s_ids'))){
            foreach (Config::get('Projectconfig.default_SomeItem5s_ids') as $default_SomeItem5_id){
                $query->whereRaw(
                    'SomeItem2s.SomeItem2_id NOT IN ( 
                        SELECT hide_SomeItem2_id FROM hides WHERE hide_SomeItem5_id = ? AND hide_SomeItem1_id = ? AND deleted_at IS NULL
                    )',
                    [$default_SomeItem5_id, $SomeItem1->SomeItem1_id]
                );
            }
        }


        if($SomeItem4s && $SomeItem1!=null){

            if($all_SomeItem4s){
                $SomeItem4s_query = SomeItem2::where('SomeItem2_path', 'like', $SomeItem5->SomeItem5_id.'/SomeItem4s/%')
                    ->where('SomeItem2_is_SomeItem4', '1')
                    ->where('SomeItem2_offline', null);
            }
            else{
                $SomeItem4s_query = SomeItem2::where('SomeItem2_path', 'like', $SomeItem5->SomeItem5_id.'/SomeItem4s/'.$SomeItem1->SomeItem1_id)
                    ->where('SomeItem2_is_SomeItem4', '1')
                    ->where('SomeItem2_offline', null)
                    ->where('SomeItem2_SomeItem6_id', $LeveledItemOne_id);
            }

            $query->union($SomeItem4s_query)->orderBy('SomeItem2_is_SomeItem4', 'desc');

        }

        if($sorting_map[$sorting]!='relevance'){
            $query->orderBy($sorting_map[$sorting], 'desc');
        }
        else{
            $query->orderBy('SomeItem2_SomeItem8', 'desc'); //RELEVANCE COLUMN NEEDED
        }


        $query = $query->select('SomeItem2s.*')
            ->with(array(
                'parentSomeItem6'=>function($query){
                    $query->select('SomeItem6_id', 'SomeItem6_name', 'SomeItem6_display_name');
                }));


        $SomeItem2s = $query->skip($pagination*($page-1))
            ->take($pagination+1)
            ->get();

        $response =[];

        $end_of_list = true;

        if($SomeItem2s->count()>$pagination){
            $SomeItem2s->pop();
            $end_of_list = false;
        }

        foreach ($SomeItem2s as $SomeItem2){

            if($SomeItem2->SomeItem2_is_SomeItem4){
                $meta=[
                    'is_favorite'=>false,
                    'is_wish'=>false,
                    'is_hidden'=>false,
                    'SomeItem2_SomeItem5_SomeItem8'=>null,
                    'SomeItem2_SomeItem5_SomeItem7'=>null,
                    'SomeItem2_last_editor_SomeItem8'=>null,
                    'SomeItem9s'=>null
                ];
            }
            else{
                $meta=[
                    'is_favorite'=>$SomeItem2->isFavorite($SomeItem5),
                    'is_wish'=>$SomeItem2->isWish($SomeItem5),
                    'is_hidden'=>$SomeItem1==null ? false : $SomeItem2->isHidden($SomeItem1, $SomeItem5),
                    'SomeItem2_SomeItem5_SomeItem8'=>$SomeItem2->SomeItem5SomeItem8($SomeItem5) == null ? null : $SomeItem2->SomeItem5SomeItem8($SomeItem5)->SomeItem8_SomeItem8,
                    'SomeItem2_SomeItem5_SomeItem7'=>$SomeItem2->SomeItem5SomeItem7($SomeItem5) == null ? null : $SomeItem2->SomeItem5SomeItem7($SomeItem5)->SomeItem7_content,
                    'SomeItem2_last_editor_SomeItem8'=>$SomeItem2->lastEditorSomeItem8() == null ? null : $SomeItem2->lastEditorSomeItem8()->SomeItem8_SomeItem8,
                    'SomeItem2_SomeItem9s'=>$SomeItem2->SomeItem9s()->get()
                ];
            }

            $response[] = ['SomeItem2' => $SomeItem2, 'meta'=>$meta];
        }

        return $this->response(['SomeItem2s' => $response, 'end_of_list' => $end_of_list]);

    }

    public function SomeItem2sFromLeveledItemOneLeveledItemTwos(Request $request, $SomeItem1, $volume, SomeItem5 $SomeItem5)
    {
        $LeveledItemTwos=[];

        foreach($request->data['LeveledItemTwos'] as $LeveledItemTwo){
            if($LeveledItemTwo['selected']) {
                $LeveledItemTwos[] = $LeveledItemTwo['SomeItem6_id'];
            }
        }

        $SomeItem9s = [];


        foreach( (array) $request->data['SomeItem9s'] as $SomeItem9){
            $SomeItem9s[]=[
                'SomeItem9_id' => $SomeItem9['SomeItem9']['SomeItem9_id'],
                'value'=>$SomeItem9['value'],
                'checkbox_value'=>'on'
            ];
        }

        $hidden = $request->data['hidden'];

        $sorting = $request->data['sorting'];

        $sorting_map = [
            'relevance',
            'relevance',
            'SomeItem2_SomeItem8',
            'SomeItem2_num_pages',
            'SomeItem2_hits',
            'SomeItem2_SomeItem7_count',
            'SomeItem2_favourite_count',
            'SomeItem2_extraction_hits',
            'SomeItem2_id',
            'SomeItem2_id'
        ];

        $SomeItem4s = $request->data['SomeItem4s'];

        $all_SomeItem4s = $request->data['all_SomeItem4s'];

        $pagination = $request->data['pagination'];

        $page = $request->data['page'];

        $option = $request->data['option'];

        $LeveledItemOne_id = $request->data['LeveledItemOne_id'];


        $response =[];

        $sorted_SomeItem2s = SomeItem1::getSomeItem2sForLeveledItemTwos($option, $SomeItem5, $SomeItem1, $hidden,
            $SomeItem4s, $all_SomeItem4s, $LeveledItemOne_id, $LeveledItemTwos,
            $SomeItem9s, $volume, $pagination, $page,
            $sorting, $sorting_map);


        foreach ($sorted_SomeItem2s['SomeItem2s'] as $SomeItem2){

            if($SomeItem2->SomeItem2_is_SomeItem4){
                $meta=[
                    'is_favorite'=>false,
                    'is_wish'=>false,
                    'is_hidden'=>false,
                    'SomeItem2_SomeItem5_SomeItem8'=>null,
                    'SomeItem2_SomeItem5_SomeItem7'=>null,
                    'SomeItem2_last_editor_SomeItem8'=>null,
                    'SomeItem9s'=>null,
                    'related_SomeItem1'=> $SomeItem2->relatedSomeItem1()
                ];
            }
            else{
                $meta=[
                    'is_favorite'=>$SomeItem2->isFavorite($SomeItem5),
                    'is_wish'=>$SomeItem2->isWish($SomeItem5),
                    'is_hidden'=>$SomeItem1==null ? false : $SomeItem2->isHidden($SomeItem1, $SomeItem5),
                    'SomeItem2_SomeItem5_SomeItem8'=>$SomeItem2->SomeItem5SomeItem8($SomeItem5) == null ? null : $SomeItem2->SomeItem5SomeItem8($SomeItem5)->SomeItem8_SomeItem8,
                    'SomeItem2_SomeItem5_SomeItem7'=>$SomeItem2->SomeItem5SomeItem7($SomeItem5) == null ? null : $SomeItem2->SomeItem5SomeItem7($SomeItem5)->SomeItem7_content,
                    'SomeItem2_last_editor_SomeItem8'=>$SomeItem2->lastEditorSomeItem8() == null ? null : $SomeItem2->lastEditorSomeItem8()->SomeItem8_SomeItem8,
                    'SomeItem2_SomeItem9s'=>$SomeItem2->SomeItem9s()->get(),
                    'related_SomeItem1'=> null
                ];
            }

            $response[] = ['SomeItem2' => $SomeItem2, 'meta'=>$meta];
        }

        return $this->response(['SomeItem2s' => $response, 'end_of_list' => $sorted_SomeItem2s['end_of_list']]);

    }


    public function SomeItem2sFromLeveledItemOneLeveledItemTwosNoSomeItem1(Request $request, SomeItem5 $SomeItem5)
    {
        return $this->SomeItem2sFromLeveledItemOneLeveledItemTwos($request, null, null, $SomeItem5);
    }


    public function rateSomeItem1(Request $request, SomeItem1 $SomeItem1, SomeItem5 $SomeItem5)
    {
        $SomeItem1->rate($SomeItem5, $request->data['SomeItem8']);

        return $this->response([
            'SomeItem1_average_SomeItem8'=>$SomeItem1->averageSomeItem8(),
            'SomeItem1_number_votes' => $SomeItem1->SomeItem8s()->count()
        ]);
    }


    public function SomeItem7SomeItem1(Request $request, SomeItem1 $SomeItem1, SomeItem5 $SomeItem5)
    {
        $SomeItem1->SomeItem7($SomeItem5, $request->data['SomeItem7']);

        return $this->response('SomeItem7ed');
    }


    public function img(SomeItem1 $SomeItem1)
    {
        return $SomeItem1->img($SomeItem1);
    }

    public function SomeItem7s(SomeItem1 $SomeItem1, SomeItem5 $SomeItem5)
    {
        return $this->response($SomeItem1->approvedSomeItem7s()->get());
    }


    public function deleteSomeItem1(SomeItem1 $SomeItem1, SomeItem5 $SomeItem5)
    {
        $SomeItem1->removeSomeItem1($SomeItem5);
        return $this->response(true);
    }

    public function uploadRandomSomeItem1s(Request $request, SomeItem5 $SomeItem5)
    {
        $SomeItem1s_to_take = Config::get('Projectconfig.random_SomeItem1s_count');

        $SomeItem1s=[];

        if(!isset($request->data['option'])){
            return response('Not found', 404);
        }
        elseif($request->data['option'] == 'default'){
            $SomeItem1s = SomeItem1::with('group')
                ->whereIn('SomeItem1_owner_id', Config::get('Projectconfig.default_SomeItem5s_ids'))
                ->inRandomOrder()
                ->take($SomeItem1s_to_take)
                ->get();
        }
        elseif($request->data['option'] == 'SomeItem5'){
            $SomeItem1s = SomeItem1::with('group')
                ->where('SomeItem1_owner_id', $SomeItem5->SomeItem5_id)
                ->inRandomOrder()
                ->take($SomeItem1s_to_take)
                ->get();
        }
        else{
            return response('Not found', 404);
        }

        $response=[];

        foreach ($SomeItem1s as $SomeItem1){

            $response[]=[
                'SomeItem1'=>$SomeItem1,
                'SomeItem1_average_SomeItem8'=> $SomeItem1->averageSomeItem8(),
            ];
        }

        return $this->response($response);
    }


}
