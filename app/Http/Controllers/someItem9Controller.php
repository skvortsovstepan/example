<?php

namespace App\Http\Controllers;

use App\SomeItem2Flag;
use App\SomeItem9;
use App\SomeItem5;
use Illuminate\Http\Request;

class SomeItem9Controller extends Controller
{
    public function SomeItem9s(SomeItem5 $SomeItem5)
    {
        $data = SomeItem9::all();

        return $this->response($data);
    }

    public function SomeItem9sWithFlags(SomeItem5 $SomeItem5)
    {
        $flags=[];

        foreach (SomeItem2Flag::all() as $flag){
            $flags[] = ['value'=> $flag->SomeItem2_flag_id, 'text'=> $flag->SomeItem2_flag_name];
        }

        $data = ['SomeItem9s' => SomeItem9::all(), 'flags'=>$flags];

        return $this->response($data);
    }
}
