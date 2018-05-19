<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use __project__\Facades\__some__auth_service__;

class __some__item_1__sGroup extends Model
{
    protected $primaryKey = '__some__item_1___group_id';

    protected $guarded = [];

    public static function default__some__item_1__sGroupsCollection()
    {
        return __some__item_1__sGroup::whereIn('__some__item_1___group___some__item__5__id', \Config::get('__project__config.default___some__item__5_s_ids'))->get();
    }

    public function __some__item_1__s()
    {
        return $this->hasMany('App\__some__item_1__', '__some__item_1___group');
    }

    public static function findOrCreate($value, $is_new)
    {
        if($is_new == true){

            $group = new __some__item_1__sGroup();

            $group->fill([
                '__some__item_1___group_display_name' => $value,
                '__some__item_1___group___some__item__5__id' => __some__auth_service__::id()
            ]);

            $group->save();

            return $group;
        }

        return __some__item_1__sGroup::find($value);
    }

    public function __some__item_1__sCountFor__some__item__5_(__some__item__5_ $__some__item__5_)
    {
        return $this->__some__item_1__s()->where('__some__item_1___owner_id', $__some__item__5_->__some__item__5__id)->count();
    }

    public function default__some__item_1__sCount()
    {
        return $this->__some__item_1__s()->whereIn('__some__item_1___owner_id', \Config::get('__project__config.default___some__item__5_s_ids'))->count();
    }

    public function checkEmptiness()
    {
        if($this->__some__item_1__s()->count() == 0){
            $this->delete();
            return true;
        }

        return false;
    }
}
