<?php

namespace App\Http\Controllers;

use App\__some_item_7__;
use App\__some__item__5_;
use Illuminate\Http\Request;

use App\Http\Requests;

class __some_item_7__Controller extends Controller
{
    public function __some_item_2____some_item_7__s()
    {
        return $this->response(__some_item_7__::where('__some_item_7___type', '__some_item_2__')
            ->with(array('__some__item__5_'=>function($query){
                $query->select('__some__item__5__id','login', 'name_f', 'name_l');
            }))
            ->with('__some_item_2__')
            ->get()
        );
    }



    public function __some__item_1____some_item_7__s()
    {
        return $this->response(__some_item_7__::where('__some_item_7___approved', null)
            ->where('__some_item_7___type', '__some__item_1__')
            ->with(array('__some__item__5_'=>function($query){
                $query->select('__some__item__5__id','login', 'name_f', 'name_l');
            }))
            ->with('__some__item_1__')
            ->get()
        );
    }

    public function approve__some_item_7__(Request $request, __some__item__5_ $__some__item__5_)
    {
        $__some_item_7__ = __some_item_7__::find($request->data['__some_item_7___id']);

        if(!$__some_item_7__){
            return response('', 404);
        }

        $__some_item_7__->approve();

        return $this->response(true);
    }

    public function delete__some_item_7__(Request $request, __some__item__5_ $__some__item__5_)
    {
        $__some_item_7__ = __some_item_7__::find($request->data['__some_item_7___id']);

        if(!$__some_item_7__){
            return response('', 404);
        }

        $__some_item_7__->remove();

        return $this->response(true);
    }

    public function __some_item_2__(Request $request, __some__item__5_ $__some__item__5_)
    {
        $__some_item_7__ = __some_item_7__::find($request->data['__some_item_7___id']);

        if(!$__some_item_7__ || $__some_item_7__->__some_item_7___type!='__some_item_2__'){
            return response('', 404);
        }

        $__some_item_2__ = $__some_item_7__->__some_item_2__()
            ->with(array(
            'parent__some__item__6_'=>function($query){
                $query->select('__some__item__6__id', '__some__item__6__name', '__some__item__6__display_name');
            }))
            ->first();

        if(!$__some_item_2__){
            return response('', 404);
        }


        $meta=[
            'is_favorite'=>$__some_item_2__->isFavorite($__some__item__5_),
            'is_wish'=>$__some_item_2__->isWish($__some__item__5_),
            'is_hidden'=> false,
            '__some_item_2_____some__item__5____some_item_8__'=>$__some_item_2__->__some__item__5___some_item_8__($__some__item__5_) == null ? null : $__some_item_2__->__some__item__5___some_item_8__($__some__item__5_)->__some_item_8_____some_item_8__,
            '__some_item_2_____some__item__5____some_item_7__'=>$__some_item_2__->__some__item__5___some_item_7__($__some__item__5_) == null ? null : $__some_item_2__->__some__item__5___some_item_7__($__some__item__5_)->__some_item_7___content,
            '__some_item_2___last_editor___some_item_8__'=>$__some_item_2__->lastEditor__some_item_8__() == null ? null : $__some_item_2__->lastEditor__some_item_8__()->__some_item_8_____some_item_8__,
            '__some_item_2_____some_item_9__s'=>$__some_item_2__->__some_item_9__s()->get()
        ];


        return $this->response(['__some_item_2__' => $__some_item_2__, 'meta'=>$meta, '__leveled_item_one__' => $__some_item_2__->__leveled_item_one__()->first()]);
    }
}
