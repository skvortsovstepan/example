<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SomeItem7 extends Model
{

    protected $primaryKey = 'SomeItem7_id';

    protected $guarded = [];

    public function SomeItem5(){
        return $this->belongsTo('App\SomeItem5', 'SomeItem7_SomeItem5_id');
    }

    // Warning! No SomeItem7_type detection
    public function SomeItem2()
    {
        return $this->belongsTo('App\SomeItem2', 'SomeItem7_SomeItem7ed_id');
    }

    // Warning! No SomeItem7_type detection
    public function SomeItem1()
    {
        return $this->belongsTo('App\SomeItem1', 'SomeItem7_SomeItem7ed_id');
    }

    public function approve()
    {
        $was_approved = false;

        if($this->SomeItem7_approved == 1){
            $was_approved = true;
        }

        $this->SomeItem7_approved = 1;

        $this->save();

        if($this->SomeItem7_type == 'SomeItem2' && !$was_approved){
            $SomeItem2 = $this->SomeItem2()->first();

            $SomeItem2->SomeItem2_SomeItem7_count++;

            $SomeItem2->save();
        }
    }

    public function remove()
    {
        if($this->SomeItem7_type == 'SomeItem2'){
            $SomeItem2 = $this->SomeItem2()->first();

            $SomeItem2->SomeItem2_SomeItem7_count--;

            $SomeItem2->save();
        }

        $this->delete();
    }
}
