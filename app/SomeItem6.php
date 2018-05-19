<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Project\Sync\SomeItem6 as SomeItem6Interface;

class SomeItem6 extends Model implements SomeItem6Interface
{
    protected $primaryKey = 'SomeItem6_id';

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo('App\SomeItem6', 'SomeItem6_parent');
    }

    public function childs()
    {
        return $this->hasMany('App\SomeItem6', 'SomeItem6_parent');
    }

    public function SomeItem2s()
    {
        return $this->hasMany('App\SomeItem2', 'SomeItem2_SomeItem6_id');
    }

    public function metas()
    {
        if($this->SomeItem6_type == \Config::get('Projectconfig.depth_type_map')[1]){
            return DB::table('SomeItem1s_meta')->where('SomeItem1_meta_LeveledItemThree_id', $this->SomeItem6_id);
        }
        elseif($this->SomeItem6_type == \Config::get('Projectconfig.depth_type_map')[3]){
            return DB::table('SomeItem1s_meta')->where('SomeItem1_meta_LeveledItemTwo_id', $this->SomeItem6_id);
        }
        return DB::table('SomeItem1s_meta')->where('SomeItem1_meta_id', -1); // Empty builder
    }

    public function LeveledItemOnes()
    {
        if($this->SomeItem6_type != \Config::get('Projectconfig.depth_type_map')[1]){
            return null;
        }

        return $this->childs();
    }

    public function LeveledItemTwos()
    {
        if($this->SomeItem6_type != \Config::get('Projectconfig.depth_type_map')[1]){
            return null;
        }

        $ids = [];

        foreach ($this->LeveledItemOnes() as $LeveledItemOne){
            $ids[] = $LeveledItemOne->id;
        }

        return SomeItem6::whereIn('SomeItem6_parents_id', $ids);
    }

}
