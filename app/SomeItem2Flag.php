<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SomeItem2Flag extends Model
{
    protected $table = 'SomeItem2s_flags';

    protected $primaryKey = 'SomeItem2_flag_id';

    protected $guarded = [];
}
