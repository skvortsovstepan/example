<?php

namespace App\Http\Controllers;

use App\SomeItem7;
use App\SomeItem5;
use Illuminate\Http\Request;

use App\Http\Requests;

class SomeItem7Controller extends Controller
{
    public function SomeItem2SomeItem7s()
    {
        return $this->response(SomeItem7::where('SomeItem7_type', 'SomeItem2')
            ->with(array('SomeItem5'=>function($query){
                $query->select('SomeItem5_id','login', 'name_f', 'name_l');
            }))
            ->with('SomeItem2')
            ->get()
        );
    }



    public function SomeItem1SomeItem7s()
    {
        return $this->response(SomeItem7::where('SomeItem7_approved', null)
            ->where('SomeItem7_type', 'SomeItem1')
            ->with(array('SomeItem5'=>function($query){
                $query->select('SomeItem5_id','login', 'name_f', 'name_l');
            }))
            ->with('SomeItem1')
            ->get()
        );
    }

    public function approveSomeItem7(Request $request, SomeItem5 $SomeItem5)
    {
        $SomeItem7 = SomeItem7::find($request->data['SomeItem7_id']);

        if(!$SomeItem7){
            return response('', 404);
        }

        $SomeItem7->approve();

        return $this->response(true);
    }

    public function deleteSomeItem7(Request $request, SomeItem5 $SomeItem5)
    {
        $SomeItem7 = SomeItem7::find($request->data['SomeItem7_id']);

        if(!$SomeItem7){
            return response('', 404);
        }

        $SomeItem7->remove();

        return $this->response(true);
    }

    public function SomeItem2(Request $request, SomeItem5 $SomeItem5)
    {
        $SomeItem7 = SomeItem7::find($request->data['SomeItem7_id']);

        if(!$SomeItem7 || $SomeItem7->SomeItem7_type!='SomeItem2'){
            return response('', 404);
        }

        $SomeItem2 = $SomeItem7->SomeItem2()
            ->with(array(
            'parentSomeItem6'=>function($query){
                $query->select('SomeItem6_id', 'SomeItem6_name', 'SomeItem6_display_name');
            }))
            ->first();

        if(!$SomeItem2){
            return response('', 404);
        }


        $meta=[
            'is_favorite'=>$SomeItem2->isFavorite($SomeItem5),
            'is_wish'=>$SomeItem2->isWish($SomeItem5),
            'is_hidden'=> false,
            'SomeItem2_SomeItem5_SomeItem8'=>$SomeItem2->SomeItem5SomeItem8($SomeItem5) == null ? null : $SomeItem2->SomeItem5SomeItem8($SomeItem5)->SomeItem8_SomeItem8,
            'SomeItem2_SomeItem5_SomeItem7'=>$SomeItem2->SomeItem5SomeItem7($SomeItem5) == null ? null : $SomeItem2->SomeItem5SomeItem7($SomeItem5)->SomeItem7_content,
            'SomeItem2_last_editor_SomeItem8'=>$SomeItem2->lastEditorSomeItem8() == null ? null : $SomeItem2->lastEditorSomeItem8()->SomeItem8_SomeItem8,
            'SomeItem2_SomeItem9s'=>$SomeItem2->SomeItem9s()->get()
        ];


        return $this->response(['SomeItem2' => $SomeItem2, 'meta'=>$meta, 'LeveledItemOne' => $SomeItem2->LeveledItemOne()->first()]);
    }
}
