<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class __some_item_7__ extends Model
{

    protected $primaryKey = '__some_item_7___id';

    protected $guarded = [];

    public function __some__item__5_(){
        return $this->belongsTo('App\__some__item__5_', '__some_item_7_____some__item__5__id');
    }

    // Warning! No __some_item_7___type detection
    public function __some_item_2__()
    {
        return $this->belongsTo('App\__some_item_2__', '__some_item_7_____some_item_7__ed_id');
    }

    // Warning! No __some_item_7___type detection
    public function __some__item_1__()
    {
        return $this->belongsTo('App\__some__item_1__', '__some_item_7_____some_item_7__ed_id');
    }

    public function approve()
    {
        $was_approved = false;

        if($this->__some_item_7___approved == 1){
            $was_approved = true;
        }

        $this->__some_item_7___approved = 1;

        $this->save();

        if($this->__some_item_7___type == '__some_item_2__' && !$was_approved){
            $__some_item_2__ = $this->__some_item_2__()->first();

            $__some_item_2__->__some_item_2_____some_item_7___count++;

            $__some_item_2__->save();
        }
    }

    public function remove()
    {
        if($this->__some_item_7___type == '__some_item_2__'){
            $__some_item_2__ = $this->__some_item_2__()->first();

            $__some_item_2__->__some_item_2_____some_item_7___count--;

            $__some_item_2__->save();
        }

        $this->delete();
    }
}
