<?php

namespace App\Http\Controllers;

use App\__some_item_2__Flag;
use App\__some_item_9__;
use App\__some__item__5_;
use Illuminate\Http\Request;

class __some_item_9__Controller extends Controller
{
    public function __some_item_9__s(__some__item__5_ $__some__item__5_)
    {
        $data = __some_item_9__::all();

        return $this->response($data);
    }

    public function __some_item_9__sWithFlags(__some__item__5_ $__some__item__5_)
    {
        $flags=[];

        foreach (__some_item_2__Flag::all() as $flag){
            $flags[] = ['value'=> $flag->__some_item_2___flag_id, 'text'=> $flag->__some_item_2___flag_name];
        }

        $data = ['__some_item_9__s' => __some_item_9__::all(), 'flags'=>$flags];

        return $this->response($data);
    }
}
