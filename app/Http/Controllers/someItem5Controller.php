<?php

namespace App\Http\Controllers;

use App\SomeItem2;
use App\SomeItem6;
use App\SomeItem5;
use App\SomeItem5SomeItem9;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDO;
use Project\Facades\SomeAuthService;

class SomeItem5Controller extends Controller
{
    /*public function index()
    {
        SomeAuthService::checkAccess();

        return \Redirect::away(\Config::get('Projectconfig.frontend_url'));
    }*/

    public function verify()
    {
        $SomeItem5 = SomeAuthService::SomeItem5();

        if ($SomeItem5) {
            $disk = Storage::disk(\Config::get('Projectconfig.Project_SomeItem5s_disk'));

            //if(!$disk->has($SomeItem5['SomeItem5_id'])){
            if (!\SomeItem2::isDirectory(\Config::get('SomeItem2systems.disks')['Project_SomeItem5s_disk']['root'] . "/" . $SomeItem5['SomeItem5_id'])) {
                $disk->createDir($SomeItem5['SomeItem5_id']);
                $disk->createDir($SomeItem5['SomeItem5_id'] . '/SomeItem4s');
            }


            $data = [
                'SomeItem5_id' => $SomeItem5['SomeItem5_id'],
                'SomeItem5_type' => SomeAuthService::SomeItem5Type(),
                'login' => $SomeItem5['login'],
                'name_f' => $SomeItem5['name_f'],
                'name_l' => $SomeItem5['name_l'],
                'SomeItem5_properties' => array()
            ];
        } else {
            $data = $SomeItem5;
        }

        return $this->response($data);
    }


    public function login()
    {
        SomeAuthService::checkAccess();

        return \Redirect::away(\Config::get('Projectconfig.frontend_url'), 302);
    }


    public function logout()
    {
        return $this->response(SomeAuthService::logoutUrl());
    }

    public function avatar(SomeItem5 $SomeItem5)
    {
        return $SomeItem5->avatar();
    }

    public function setSomeItem5SomeItem9s(Request $request, SomeItem5 $SomeItem5)
    {
        $SomeItem9s = $request->data;

        $SomeItem5->setNewSomeItem5SomeItem9s($SomeItem9s);

        return $this->response(true);
    }

    public function getSomeItem5SomeItem9s(Request $request, SomeItem5 $SomeItem5)
    {
        return $this->response($SomeItem5->SomeItem5SomeItem9s()->get());
    }
}
