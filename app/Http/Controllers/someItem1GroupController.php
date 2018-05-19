<?php

namespace App\Http\Controllers;

use App\SomeItem1sGroup;
use App\SomeItem5;
use Illuminate\Http\Request;

use App\Http\Requests;

class SomeItem1sGroupController extends Controller
{
    public function SomeItem5Groups(SomeItem5 $SomeItem5)
    {
        return $this->response($SomeItem5->ownSomeItem1sGroups()->get());
    }

    public function defaultGroups()
    {
        return $this->response(SomeItem1sGroup::defaultSomeItem1sGroupsCollection());
    }

    public function groups(Request $request, SomeItem5 $SomeItem5)
    {
        $response=[];

        if($request->data['SomeItem1_option'] == 'SomeItem5'){
            $groups = $SomeItem5->allSomeItem1sGroupsCollection();

            foreach ($groups as $group){
                $response[]=[
                    'SomeItem1_group' => $group,
                    'SomeItem1s_count' => $group->SomeItem1sCountForSomeItem5($SomeItem5)
                ];
            }
        }
        // default
        else{
            $groups = SomeItem1sGroup::defaultSomeItem1sGroupsCollection();

            foreach ($groups as $group){
                $response[]=[
                    'SomeItem1_group' => $group,
                    'SomeItem1s_count' => $group->defaultSomeItem1sCount()
                ];
            }
        }

        return $this->response($response);
    }

    public function updateName(Request $request, SomeItem5 $SomeItem5)
    {
        $group = SomeItem1sGroup::find($request->get('pk'));

        if(!$group){
            return $this->response('Not found', 404);
        }

        $group->SomeItem1_group_display_name = $request->get('value');

        $group->save();

        return $this->response(true);
    }
}
