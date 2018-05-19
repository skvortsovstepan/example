<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use __project__\Sync\__some__item__6_ as __some__item__6_Interface;

class __some__item__6_ extends Model implements __some__item__6_Interface
{
    protected $primaryKey = '__some__item__6__id';

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo('App\__some__item__6_', '__some__item__6__parent');
    }

    public function childs()
    {
        return $this->hasMany('App\__some__item__6_', '__some__item__6__parent');
    }

    public function __some_item_2__s()
    {
        return $this->hasMany('App\__some_item_2__', '__some_item_2_____some__item__6__id');
    }

    public function metas()
    {
        if($this->__some__item__6__type == \Config::get('__project__config.depth_type_map')[1]){
            return DB::table('__some__item_1__s_meta')->where('__some__item_1___meta___leveled_item_three___id', $this->__some__item__6__id);
        }
        elseif($this->__some__item__6__type == \Config::get('__project__config.depth_type_map')[3]){
            return DB::table('__some__item_1__s_meta')->where('__some__item_1___meta___leveled_item_two___id', $this->__some__item__6__id);
        }
        return DB::table('__some__item_1__s_meta')->where('__some__item_1___meta_id', -1); // Empty builder
    }

    public function __leveled_item_one__s()
    {
        if($this->__some__item__6__type != \Config::get('__project__config.depth_type_map')[1]){
            return null;
        }

        return $this->childs();
    }

    public function __leveled_item_two__s()
    {
        if($this->__some__item__6__type != \Config::get('__project__config.depth_type_map')[1]){
            return null;
        }

        $ids = [];

        foreach ($this->__leveled_item_one__s() as $__leveled_item_one__){
            $ids[] = $__leveled_item_one__->id;
        }

        return __some__item__6_::whereIn('__some__item__6__parents_id', $ids);
    }

}
