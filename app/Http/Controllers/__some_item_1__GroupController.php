<?php

namespace App\Http\Controllers;

use App\__some__item_1__sGroup;
use App\__some__item__5_;
use Illuminate\Http\Request;

use App\Http\Requests;

class __some__item_1__sGroupController extends Controller
{
    public function __some__item__5_Groups(__some__item__5_ $__some__item__5_)
    {
        return $this->response($__some__item__5_->own__some__item_1__sGroups()->get());
    }

    public function defaultGroups()
    {
        return $this->response(__some__item_1__sGroup::default__some__item_1__sGroupsCollection());
    }

    public function groups(Request $request, __some__item__5_ $__some__item__5_)
    {
        $response=[];

        if($request->data['__some__item_1___option'] == '__some__item__5_'){
            $groups = $__some__item__5_->all__some__item_1__sGroupsCollection();

            foreach ($groups as $group){
                $response[]=[
                    '__some__item_1___group' => $group,
                    '__some__item_1__s_count' => $group->__some__item_1__sCountFor__some__item__5_($__some__item__5_)
                ];
            }
        }
        // default
        else{
            $groups = __some__item_1__sGroup::default__some__item_1__sGroupsCollection();

            foreach ($groups as $group){
                $response[]=[
                    '__some__item_1___group' => $group,
                    '__some__item_1__s_count' => $group->default__some__item_1__sCount()
                ];
            }
        }

        return $this->response($response);
    }

    public function updateName(Request $request, __some__item__5_ $__some__item__5_)
    {
        $group = __some__item_1__sGroup::find($request->get('pk'));

        if(!$group){
            return $this->response('Not found', 404);
        }

        $group->__some__item_1___group_display_name = $request->get('value');

        $group->save();

        return $this->response(true);
    }
}
