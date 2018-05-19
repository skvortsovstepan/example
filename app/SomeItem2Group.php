<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Project\Facades\SomeAuthService;

class SomeItem1sGroup extends Model
{
    protected $primaryKey = 'SomeItem1_group_id';

    protected $guarded = [];

    public static function defaultSomeItem1sGroupsCollection()
    {
        return SomeItem1sGroup::whereIn('SomeItem1_group_SomeItem5_id', \Config::get('Projectconfig.default_SomeItem5s_ids'))->get();
    }

    public function SomeItem1s()
    {
        return $this->hasMany('App\SomeItem1', 'SomeItem1_group');
    }

    public static function findOrCreate($value, $is_new)
    {
        if($is_new == true){

            $group = new SomeItem1sGroup();

            $group->fill([
                'SomeItem1_group_display_name' => $value,
                'SomeItem1_group_SomeItem5_id' => SomeAuthService::id()
            ]);

            $group->save();

            return $group;
        }

        return SomeItem1sGroup::find($value);
    }

    public function SomeItem1sCountForSomeItem5(SomeItem5 $SomeItem5)
    {
        return $this->SomeItem1s()->where('SomeItem1_owner_id', $SomeItem5->SomeItem5_id)->count();
    }

    public function defaultSomeItem1sCount()
    {
        return $this->SomeItem1s()->whereIn('SomeItem1_owner_id', \Config::get('Projectconfig.default_SomeItem5s_ids'))->count();
    }

    public function checkEmptiness()
    {
        if($this->SomeItem1s()->count() == 0){
            $this->delete();
            return true;
        }

        return false;
    }
}
