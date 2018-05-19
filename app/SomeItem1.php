<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDO;
use Project\Facades\SomeAuthService;

class SomeItem1 extends Model
{
    protected $primaryKey = 'SomeItem1_id';

    protected $guarded = [];

    public function SomeItem9s()
    {
        return $this->belongsToMany('App\SomeItem9', 'SomeItem1s_meta', 'SomeItem1_meta_SomeItem1_id', 'SomeItem1_meta_SomeItem9_id')
            ->withPivot('SomeItem1_meta_SomeItem9_value')
            ->withTimestamps();
    }

    public function LeveledItemTwos()
    {
        return $this->belongsToMany('App\SomeItem6', 'SomeItem1s_meta', 'SomeItem1_meta_SomeItem1_id', 'SomeItem1_meta_LeveledItemTwo_id')
            ->where('SomeItem6_offline', null)
            ->withTimestamps();
    }

    public function SomeItem2sCount()
    {
        $query_LeveledItemTwos = DB::table('SomeItem1s')
            ->whereRaw('SomeItem1s.SomeItem1_id = ' . $this->SomeItem1_id)
            ->select(
                'SomeItem1_id',
                DB::raw('(
                    SELECT COUNT(1) 
                    FROM SomeItem2s 
                    WHERE SomeItem2s.SomeItem2_offline IS NULL 
                        AND SomeItem2s.SomeItem2_is_SomeItem4 IS NULL 
                        AND SomeItem2s.SomeItem2_SomeItem6_id IN (
                            SELECT SomeItem1_meta_LeveledItemTwo_id 
                            FROM SomeItem1s_meta
                            WHERE SomeItem1_meta_SomeItem1_id = SomeItem1_id
                        ) 
                ) AS SomeItem2s_number')
            );

        $query_all = DB::table('SomeItem1s')
            ->whereRaw('SomeItem1s.SomeItem1_id = ' . $this->SomeItem1_id)
            ->whereRaw('SomeItem1_id NOT IN (SELECT SomeItem1_meta_SomeItem1_id FROM SomeItem1s_meta WHERE SomeItem1_meta_LeveledItemTwo_id IS NOT NULL)')
            ->select(
                'SomeItem1_id',
                DB::raw('0 AS SomeItem2s_number')
            )
            ->union($query_LeveledItemTwos)
            ->toSql();

        DB::setFetchMode(PDO::FETCH_ASSOC);
        $result = DB::select($query_all);

        if (!isset($result[0])) {
            return 0;
        }

        return $result[0]['SomeItem2s_number'];
    }

    public function LeveledItemTwosCount()
    {
        return $this->LeveledItemTwos()->count();
    }

    public function LeveledItemOnesCount()
    {
        // this will be easier to define through hasManyThrough,
        // but this relation does not allow sorting on MySQL side.
        // So for now the code from sorting is used here.

        $query_no_LeveledItemOnes = DB::table('SomeItem1s')
            ->whereRaw('SomeItem1_id = ' . $this->SomeItem1_id)
            ->whereRaw('SomeItem1_id NOT IN (SELECT SomeItem1_meta_SomeItem1_id FROM SomeItem1s_meta WHERE SomeItem1_meta_LeveledItemTwo_id IS NOT NULL)')
            ->select(
                'SomeItem1_id',
                DB::raw('0 AS LeveledItemOnes_number')
            )->toSql();

        $query_all = DB::table('SomeItem1s')
            ->whereRaw('SomeItem1_id = ' . $this->SomeItem1_id)
            ->leftjoin('SomeItem1s_meta as SomeItem1s_meta_LeveledItemTwos', 'SomeItem1s.SomeItem1_id', 'SomeItem1s_meta_LeveledItemTwos.SomeItem1_meta_SomeItem1_id')
            /*
             * Hidden Logic
             * */
            ->groupBy('SomeItem1s.SomeItem1_id', 'SomeItem6s.SomeItem6_parent')
            ->orderBy('SomeItem1s.SomeItem1_id', 'asc')
            ->toSql();

        $query_all = 'SELECT SomeItem1_id, COUNT(SomeItem6_parent) as LeveledItemOnes_number 
                          FROM /*Hidden*/ 
                          GROUP BY SomeItem1_id 
                          UNION '/*Hidden*/ . '
                          ORDER BY  /*Hidden*/ DESC,  /*Hidden*/ ASC';

        DB::setFetchMode(PDO::FETCH_ASSOC);

        $result = DB::select($query_all);

        if (!isset($result[0])) {
            return 0;
        }

        return $result[0]['LeveledItemOnes_number'];
    }

    public function LeveledItemThreesCount()
    {
        return $this->LeveledItemThrees()->count();
    }

    public function doesLeveledItemOneBelong(SomeItem6 $LeveledItemOne)
    {
        $collection = $this->LeveledItemTwos()->where('SomeItem6_parent', $LeveledItemOne->SomeItem6_id)->get();

        return $collection->count() >= 1 ? true : false;
    }

    public function LeveledItemThrees()
    {
        return $this->belongsToMany('App\SomeItem6', 'SomeItem1s_meta', 'SomeItem1_meta_SomeItem1_id', 'SomeItem1_meta_LeveledItemThree_id')
            ->where('SomeItem6s.SomeItem6_offline', null)
            ->withPivot('SomeItem1_meta_results_volume')
            ->withTimestamps();
    }


    // $data must contain array with keys:
    // SomeItem1_name, description,
    // group=>[value, is_new],
    // SomeItem9s => [[SomeItem9_id, value, checkbox_value],...]
    public static function buildOrUpdate($data)
    {
        $SomeItem1s_group_to_check = null;

        if ($data['SomeItem1_id'] != null) {
            $SomeItem1 = SomeItem1::find($data['SomeItem1_id']);

            $SomeItem1s_group_to_check = $SomeItem1->SomeItem1_group;

            $SomeItem1->clear();
        } else {
            $SomeItem1 = new SomeItem1();
        }


        $SomeItem1->SomeItem1_name = $data['SomeItem1_name'];

        $SomeItem1->SomeItem1_description = $data['description'];

        $SomeItem1->SomeItem1_owner_id = SomeAuthService::id();

        $group = SomeItem1sGroup::findOrCreate($data['group']['value'], $data['group']['is_new']);

        $SomeItem1->SomeItem1_group = empty($group) ? null : $group->SomeItem1_group_id;

        $SomeItem1->save();

        foreach ($data['SomeItem9s'] as $SomeItem9) {
            if ($SomeItem9['checkbox_value'] == 'on') {
                $SomeItem1->SomeItem9s()->attach($SomeItem9['SomeItem9_id'], ['SomeItem1_meta_SomeItem9_value' => $SomeItem9['value']]);
            }
        }

        $SomeItem1_disk = Storage::disk(Config::get('Projectconfig.Project_SomeItem1s_disk'));

        if (!\SomeItem2::isDirectory(Config::get('SomeItem2systems.disks')['Project_SomeItem1s_disk']['root'] . "/" . $SomeItem1->SomeItem1_id)) {
            $SomeItem1_disk->createDir($SomeItem1->SomeItem1_id);
        }

        $SomeItem1->saveImg($data['SomeItem1_img_names'], $data['SomeItem1_default_icon']);

        if ($SomeItem1s_group_to_check != null) {
            $group = SomeItem1sGroup::find($SomeItem1s_group_to_check);

            if ($group != false) {
                $group->checkEmptiness();
            }

        }

        return $SomeItem1;
    }

    public function saveImg($img_names, $default_icon)
    {
        $tmp_disk = Storage::disk(Config::get('Projectconfig.Project_tmp_disk'));

        $SomeItem1_disk = Storage::disk(Config::get('Projectconfig.Project_SomeItem1s_disk'));

        if ($img_names) {
            $length = count($img_names);

            if ($length > 1) {
                $img_name_to_parse = $img_names[$length - 1];
            } else {
                $img_name_to_parse = $img_names[0];
            }

            $tmp_name = substr($img_name_to_parse, 0, strripos($img_name_to_parse, '_'));

            $tmp_name = substr($tmp_name, 0, strripos($tmp_name, '_'));

            $img_path = $this->SomeItem1_id . '/' . $tmp_name;

            $img_content = $tmp_disk->read($img_name_to_parse);

            $SomeItem1_disk->put($img_path, $img_content);

            foreach ($img_names as $img_name) {
                $tmp_disk->delete($img_name);
            }

            $this->SomeItem1_icon = $img_path;

        } else {
            if ($this->SomeItem1_icon != $default_icon && $default_icon != null) {
                $img_content = $SomeItem1_disk->read($default_icon);

                $default_name = substr($default_icon, strripos($default_icon, '/') + 1);

                $img_path = $this->SomeItem1_id . '/' . $default_name;

                $SomeItem1_disk->put($img_path, $img_content);

                $this->SomeItem1_icon = $img_path;
            }


            if ($this->SomeItem1_icon == null && $SomeItem1_disk->has(Config::get('Projectconfig.SomeItem1_default_SomeItem6') . '/' . Config::get('Projectconfig.SomeItem1_icon_default'))) {
                $img_path = $this->SomeItem1_id . '/' . Config::get('Projectconfig.SomeItem1_icon_default');

                $img_content = $SomeItem1_disk->read(Config::get('Projectconfig.SomeItem1_default_SomeItem6') . '/' . Config::get('Projectconfig.SomeItem1_icon_default'));

                $SomeItem1_disk->put($img_path, $img_content);

                $this->SomeItem1_icon = $img_path;
            }
        }

        $this->save();
    }


    // $data must contain array with keys:
    // id,
    // selected_items=>[[],...],
    // volume_slider
    public function attachLeveledItemTwos($data)
    {
        // To keep in SomeItem1 only chosen LeveledItemThrees, the check on emptiness is needed

        $not_empty = false;

        foreach ($data['selected_items'] as $LeveledItemOne) {
            foreach ($LeveledItemOne as $LeveledItemTwo_id) {
                $this->LeveledItemTwos()->attach($LeveledItemTwo_id);
                $not_empty = true;
            }
        }

        if ($not_empty) {
            $this->LeveledItemThrees()->attach($data['id'], ['SomeItem1_meta_results_volume' => $data['volume_slider']]);
        }
    }

    public function clear()
    {
        $this->SomeItem1_name = null;

        $this->SomeItem1_description = null;

        $this->SomeItem1_group = null;

        $this->save();

        $this->SomeItem9s()->detach();

        $this->LeveledItemTwos()->detach();

        $this->LeveledItemThrees()->detach();
    }

    public function SomeItem8s()
    {
        return $this->hasMany('App\SomeItem8', 'SomeItem8_rated_id')->where('SomeItem8_type', 'SomeItem1');
    }

    public function SomeItem7s()
    {
        return $this->hasMany('App\SomeItem7', 'SomeItem7_SomeItem7ed_id')
            ->with(array('SomeItem5' => function ($query) {
                $query->select('SomeItem5_id', 'login', 'name_f', 'name_l');
            }))
            ->where('SomeItem7_type', 'SomeItem1');
    }

    public function approvedSomeItem7s()
    {
        return $this->hasMany('App\SomeItem7', 'SomeItem7_SomeItem7ed_id')
            ->with(array('SomeItem5' => function ($query) {
                $query->select('SomeItem5_id', 'login', 'name_f', 'name_l');
            }))
            ->where('SomeItem7_approved', 1)
            ->where('SomeItem7_type', 'SomeItem1');
    }

    public function averageSomeItem8()
    {
        return $this->SomeItem8s()->avg('SomeItem8_SomeItem8');
    }

    public function group()
    {
        return $this->belongsTo('App\SomeItem1sGroup', 'SomeItem1_group');
    }

    public function SomeItem5SomeItem8(SomeItem5 $SomeItem5)
    {
        return $this->SomeItem8s()->where('SomeItem8_SomeItem5_id', $SomeItem5->SomeItem5_id)->first();
    }

    public function rate(SomeItem5 $SomeItem5, $SomeItem8)
    {
        $SomeItem5_SomeItem8 = $this->SomeItem5SomeItem8($SomeItem5);

        if ($SomeItem5_SomeItem8 == null) {
            SomeItem8::create([
                'SomeItem8_type' => 'SomeItem1',
                'SomeItem8_SomeItem5_id' => $SomeItem5->SomeItem5_id,
                'SomeItem8_SomeItem8' => $SomeItem8,
                'SomeItem8_rated_id' => $this->SomeItem1_id
            ]);
        } else {
            $SomeItem5_SomeItem8->SomeItem8_SomeItem8 = $SomeItem8;

            $SomeItem5_SomeItem8->save();
        }
    }

    public function SomeItem5SomeItem7(SomeItem5 $SomeItem5)
    {
        return $this->SomeItem7s()->where('SomeItem7_SomeItem5_id', $SomeItem5->SomeItem5_id)->first();
    }

    public function SomeItem7(SomeItem5 $SomeItem5, $SomeItem7_content)
    {
        $SomeItem5_SomeItem7 = $this->SomeItem5SomeItem7($SomeItem5);

        if ($SomeItem5_SomeItem7 == null && $SomeItem7_content != '') {
            $SomeItem5_SomeItem7 = SomeItem7::create([
                'SomeItem7_type' => 'SomeItem1',
                'SomeItem7_SomeItem5_id' => $SomeItem5->SomeItem5_id,
                'SomeItem7_content' => $SomeItem7_content,
                'SomeItem7_SomeItem7ed_id' => $this->SomeItem1_id,
                'SomeItem7_approved' => null
            ]);

            if (SomeAuthService::checkEditorPermission()) {
                $SomeItem5_SomeItem7->approve();
            }
        } elseif ($SomeItem5_SomeItem7 != null) {
            $SomeItem5_SomeItem7->SomeItem7_content = $SomeItem7_content;

            $SomeItem5_SomeItem7->SomeItem7_approved = null;

            if ($SomeItem7_content != '') {
                if (SomeAuthService::checkEditorPermission()) {
                    $SomeItem5_SomeItem7->approve();
                } else {
                    $SomeItem5_SomeItem7->save();
                }
            } else {
                $SomeItem5_SomeItem7->delete();
            }
        }
    }

    public function img()
    {
        if (!$this->SomeItem1_icon) {
            return null;
        }

        $SomeItem1_disk = Storage::disk(\Config::get('Projectconfig.Project_SomeItem1s_disk'));
        return $SomeItem1_disk->read($this->SomeItem1_icon);
    }

    private static function sqlQueryToSort($type = 'default', $sort_option = 'default', $group_id = null, SomeItem5 $SomeItem5 = null)
    {

        if (empty($sort_option)) {
            $sort_option = 'default';
        }

        if (!in_array($type, ['default', 'SomeItem5']) || empty($SomeItem5)) {
            $type = 'default';
        }

        $SomeItem5s_ids = '';

        if ($type == 'default') {
            foreach (Config::get('Projectconfig.default_SomeItem5s_ids') as $default_SomeItem5s_id) {
                $SomeItem5s_ids = $SomeItem5s_ids . ' ' . $default_SomeItem5s_id . ',';
            }
            $SomeItem5s_ids = '( ' . substr($SomeItem5s_ids, 0, -1) . ' )';
        } else {
            $SomeItem5s_ids = '(' . $SomeItem5->SomeItem5_id . ' )';
        }

        if ($sort_option == 'SomeItem2s_number') {

            $query_LeveledItemTwos = DB::table('SomeItem1s')
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                //->whereRaw('SomeItem1_id NOT IN (SELECT SomeItem1_meta_SomeItem1_id FROM SomeItem1s_meta WHERE SomeItem1_meta_SomeItem9_id IS NOT NULL)')
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('SomeItem1_group = ' . $group_id);
                })
                ->select(
                    'SomeItem1_id',
                    DB::raw('(
                    SELECT COUNT(1) 
                    FROM SomeItem2s 
                    WHERE SomeItem2s.SomeItem2_offline IS NULL 
                        /*Hidden*/ IN (
                            SELECT /*Hidden*/ 
                            FROM SomeItem1s_meta
                            WHERE /*Hidden*/
                        ) 
                ) AS SomeItem2s_number')
                );

            $query_all = DB::table('SomeItem1s')
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                ->whereRaw('SomeItem1_id NOT IN (SELECT SomeItem1_meta_SomeItem1_id FROM SomeItem1s_meta WHERE SomeItem1_meta_LeveledItemTwo_id IS NOT NULL)')
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('SomeItem1_group = ' . $group_id);
                })
                ->select(
                    'SomeItem1_id',
                    DB::raw('0 AS SomeItem2s_number')
                )
                ->union($query_LeveledItemTwos)
                ->orderBy('SomeItem2s_number', 'desc')
                ->orderBy('SomeItem1_id', 'asc')
                ->toSql();
        } elseif ($sort_option == 'average_SomeItem8') {
            $query_no_SomeItem7s = DB::table('SomeItem1s')
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('SomeItem1_group = ' . $group_id);
                })
                ->whereRaw('SomeItem1_id NOT IN (SELECT /*Hidden*/)')
                ->select(
                    'SomeItem1_id',
                    DB::raw('0 AS average_SomeItem8')
                );

            $query_all = DB::table('SomeItem1s')
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('SomeItem1_group = ' . $group_id);
                })
                ->leftjoin('SomeItem8s', 'SomeItem8s.SomeItem8_rated_id', 'SomeItem1s.SomeItem1_id')
                ->whereRaw('SomeItem8s.SomeItem8_type = "SomeItem1"')
                ->select(
                    /*Hidden*/
                )
                ->groupBy('SomeItem1s.SomeItem1_id')
                ->union($query_no_SomeItem7s)
                ->orderBy('average_SomeItem8', 'desc')
                ->orderBy('SomeItem1_id', 'asc')
                ->toSql();
        } elseif ($sort_option == 'LeveledItemTwos_number') {

            $query_no_LeveledItemTwos = DB::table('SomeItem1s')
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('SomeItem1_group = ' . $group_id);
                })
                ->whereRaw('SomeItem1_id NOT IN (SELECT SomeItem1_meta_SomeItem1_id FROM SomeItem1s_meta WHERE SomeItem1_meta_LeveledItemTwo_id IS NOT NULL)')
                ->select(
                    'SomeItem1_id',
                    DB::raw('0 AS LeveledItemTwos_number')
                );

            $query_all = DB::table('SomeItem1s')
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw(/*Hidden*/);
                })
                ->leftjoin('SomeItem1s_meta as SomeItem1s_meta_LeveledItemTwos', 'SomeItem1s.SomeItem1_id', 'SomeItem1s_meta_LeveledItemTwos.SomeItem1_meta_SomeItem1_id')
                /*Hidden*/
                ->leftjoin('SomeItem6s', 'SomeItem1s_meta_LeveledItemTwos.SomeItem1_meta_LeveledItemTwo_id', 'SomeItem6s.SomeItem6_id')
                /*Hidden*/
                ->select(
                    'SomeItem1s.SomeItem1_id',
                    DB::raw('COUNT(/*Hidden*/) AS LeveledItemTwos_number')
                )
                ->groupBy('SomeItem1s.SomeItem1_id')
                ->union($query_no_LeveledItemTwos)
                ->orderBy('LeveledItemTwos_number', 'desc')
                ->orderBy('SomeItem1_id', 'asc')
                ->toSql();

        } elseif ($sort_option == 'LeveledItemOnes_number') {

            $query_no_LeveledItemOnes = DB::table('SomeItem1s')
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('SomeItem1_group = ' . $group_id);
                })
                ->whereRaw('SomeItem1_id NOT IN (SELECT /*Hidden*/ FROM /*Hidden*/ WHERE /*Hidden*/ IS NOT NULL)')
                ->select(
                    'SomeItem1_id',
                    DB::raw('0 AS LeveledItemOnes_number')
                )->toSql();

            $query_all = DB::table('SomeItem1s')
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('SomeItem1_group = ' . $group_id);
                })
                ->leftjoin(/*Hidden*/)
                ->where('/*Hidden*/', '<>', null)
                ->leftjoin('SomeItem6s', 'SomeItem6s.SomeItem6_id', 'SomeItem1s_meta_LeveledItemTwos.SomeItem1_meta_LeveledItemTwo_id')
                ->where('SomeItem6s.SomeItem6_offline', null)
                ->where('/*Hidden*/', '<>', null)
                ->select(
                    'SomeItem1s.SomeItem1_id',
                    '/*Hidden*/'
                )
                ->groupBy('SomeItem1s.SomeItem1_id', 'SomeItem6s.SomeItem6_parent')
                ->orderBy('SomeItem1s.SomeItem1_id', 'asc')
                ->toSql();

            $query_all = 'SELECT /*Hidden*/
                          FROM (' /*Hidden*/ . ') as result 
                          GROUP BY SomeItem1_id 
                          UNION ' /*Hidden*/ . '
                          ORDER BY LeveledItemOnes_number DESC, SomeItem1_id ASC';

        } elseif ($sort_option == 'LeveledItemThrees_number') {

            $query_no_LeveledItemThrees = DB::table('SomeItem1s')
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('SomeItem1_group = ' . $group_id);
                })
                ->whereRaw('SomeItem1_id NOT IN (SELECT /*Hidden*/ FROM /*Hidden*/ WHERE /*Hidden*/ IS NOT NULL)')
                ->select(
                    'SomeItem1_id',
                    DB::raw('0 AS /*Hidden*/')
                );

            $query_all = DB::table('SomeItem1s')
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('SomeItem1_group = ' . $group_id);
                })
                ->leftjoin('/*Hidden*/ as /*Hidden*/', '/*Hidden*/./*Hidden*/', '/*Hidden*/./*Hidden*/')
                ->where('/*Hidden*/./*Hidden*/', '<>', null)
                ->leftjoin('SomeItem6s', '/*Hidden*/./*Hidden*/', 'SomeItem6s.SomeItem6_id')
                ->where('SomeItem6s.SomeItem6_offline', null)
                ->select(
                    'SomeItem1s.SomeItem1_id',
                    DB::raw('COUNT(/*Hidden*/) AS LeveledItemThrees_number')
                )
                ->groupBy('SomeItem1s.SomeItem1_id')
                ->union($query_no_LeveledItemThrees)
                ->orderBy('LeveledItemThrees_number', 'desc')
                ->orderBy('SomeItem1_id', 'asc')
                ->toSql();
        } elseif ($sort_option == 'SomeItem7s_number') {
            $query_no_SomeItem7s = DB::table('SomeItem1s')
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('SomeItem1_group = ' . $group_id);
                })
                ->whereRaw('/*Hidden*/ NOT IN (SELECT /*Hidden*/ FROM /*Hidden*/ WHERE /*Hidden*/ = "/*Hidden*/")')
                ->select(
                    'SomeItem1_id',
                    DB::raw('0 AS SomeItem7s_number')
                );

            $query_all = DB::table('SomeItem1s')
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('SomeItem1_group = ' . $group_id);
                })
                ->leftjoin('SomeItem7s', '/*Hidden*/', 'SomeItem1s.SomeItem1_id')
                ->whereRaw('SomeItem7s.SomeItem7_type = "SomeItem1"')
                ->select(
                    'SomeItem1s.SomeItem1_id',
                    DB::raw('SUM(CASE WHEN /*Hidden*/ IS NULL THEN 0 ELSE 1 END) AS /*Hidden*/')
                )
                ->groupBy('SomeItem1s.SomeItem1_id')
                ->union($query_no_SomeItem7s)
                ->orderBy('SomeItem7s_number', 'desc')
                ->orderBy('SomeItem1_id', 'asc')
                ->toSql();
        } //elseif($sort_option == 'relevancy'){}

        else {
            $query_all = SomeItem1::select(
                'SomeItem1_id',
                DB::raw('NULL as "' . $sort_option . '"')
            )
                ->whereRaw('SomeItem1_owner_id IN ' . $SomeItem5s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('SomeItem1_group = ' . $group_id);
                })
                ->toSql();
        }

        return $query_all;
    }

    public static function sortAndPaginate($type, $sort_option, $group_id, $page, $pagination, $SomeItem5)
    {
        $query = static::sqlQueryToSort($type, $sort_option, $group_id, $SomeItem5) . ' LIMIT ' . ($pagination + 1) . ' OFFSET ' . (($page - 1) * $pagination);

        DB::setFetchMode(PDO::FETCH_ASSOC);

        $results_raw = DB::select($query);

        $end_of_list = true;

        $rows_number = count($results_raw);

        if ($rows_number > $pagination) {
            $end_of_list = false;
            unset($results_raw[$rows_number - 1]);
        }

        $SomeItem1s = [];

        foreach ($results_raw as $SomeItem1_raw) {
            $SomeItem1 = SomeItem1::with('group')->find($SomeItem1_raw['SomeItem1_id']);

            $SomeItem1s[] = [
                'SomeItem1' => $SomeItem1,
                'SomeItem1_SomeItem9s' => $SomeItem1->SomeItem9s()->get(),
                'SomeItem1_average_SomeItem8' => $sort_option == 'average_SomeItem8' ? $SomeItem1_raw['average_SomeItem8'] : $SomeItem1->averageSomeItem8(),
                'SomeItem1_number_votes' => $SomeItem1->SomeItem8s()->count(),
                'SomeItem1_number_SomeItem7s' => $sort_option == 'SomeItem7s_number' ? $SomeItem1_raw['SomeItem7s_number'] : $SomeItem1->approvedSomeItem7s()->count(),
                'SomeItem1_number_SomeItem2s' => $sort_option == 'SomeItem2s_number' ? $SomeItem1_raw['SomeItem2s_number'] : $SomeItem1->SomeItem2sCount(),
                'SomeItem1_number_LeveledItemTwos' => $sort_option == 'LeveledItemTwos_number' ? $SomeItem1_raw['LeveledItemTwos_number'] : $SomeItem1->LeveledItemTwosCount(),
                'SomeItem1_number_LeveledItemOnes' => $sort_option == 'LeveledItemOnes_number' ? $SomeItem1_raw['LeveledItemOnes_number'] : $SomeItem1->LeveledItemOnesCount(),
                'SomeItem1_number_LeveledItemThrees' => $sort_option == 'LeveledItemThrees_number' ? $SomeItem1_raw['LeveledItemThrees_number'] : $SomeItem1->LeveledItemThreesCount(),

            ];
        }

        return ['SomeItem1s' => $SomeItem1s, 'end_of_list' => $end_of_list];
    }

    public function removeSomeItem1(SomeItem5 $SomeItem5)
    {
        $SomeItem1_disk = Storage::disk(Config::get('Projectconfig.Project_SomeItem1s_disk'));

        if ($SomeItem5->isDefault()) {
            SomeItem5::chunk(100, function ($SomeItem5s) {
                foreach ($SomeItem5s as $SomeItem5) {
                    $this->unbindSomeItem1FromSomeItem5($SomeItem5);
                }
            });
        } else {
            $this->unbindSomeItem1FromSomeItem5($SomeItem5);
        }

        $SomeItem1_disk->deleteDir($this->SomeItem1_id);

        DB::table('SomeItem1s_meta')->where('SomeItem1_meta_SomeItem1_id', $this->SomeItem1_id)->delete();
        DB::table('hides')->where('hide_SomeItem1_id', $this->SomeItem1_id)->delete();
        $this->SomeItem7s()->delete();
        $this->SomeItem8s()->delete();

        $SomeItem1_group_id = $this->SomeItem1_group;

        $this->delete();

        if ($SomeItem1_group_id != null) {
            $group = SomeItem1sGroup::find($SomeItem1_group_id);

            if ($group != false) {
                $group->checkEmptiness();
            }
        }
    }

    private function unbindSomeItem1FromSomeItem5(SomeItem5 $SomeItem5)
    {
        $SomeItem5s_disk = Storage::disk(Config::get('Projectconfig.Project_SomeItem5s_disk'));

        foreach ($SomeItem5s_disk->SomeItem2s($SomeItem5->SomeItem5_id . '/SomeItem4s/' . $this->SomeItem1_id) as $SomeItem2) {
            $SomeItem2_name = substr($SomeItem2, strripos($SomeItem2, '/') + 1);

            $SomeItem4 = SomeItem2::where('SomeItem2_name', $SomeItem2_name)->where('SomeItem2_is_SomeItem4', 1)->first();

            if ($SomeItem4 == false) {
                continue;
            }

            if ($SomeItem5s_disk->has($SomeItem5->SomeItem5_id . '/SomeItem4s/Unrelated/' . $SomeItem2_name)) {
                $i = 1;
                $SomeItem2_name_short = substr($SomeItem2_name, 0, strripos($SomeItem2_name, '.pdf'));
                while ($SomeItem5s_disk->has($SomeItem5->SomeItem5_id . '/SomeItem4s/Unrelated/' . $SomeItem2_name_short . '(' . $i . ').pdf')) {
                    $i++;
                }
                $SomeItem2_name = $SomeItem2_name_short . '(' . $i . ').pdf';
            }

            $SomeItem4->SomeItem2_name = $SomeItem2_name;

            $SomeItem4->SomeItem2_path = $SomeItem5->SomeItem5_id . '/SomeItem4s/Unrelated';

            $SomeItem4->save();

            $SomeItem5s_disk->move($SomeItem2, $SomeItem5->SomeItem5_id . '/SomeItem4s/Unrelated/' . $SomeItem2_name);
        }

        $SomeItem5s_disk->deleteDir($SomeItem5->SomeItem5_id . '/SomeItem4s/' . $this->SomeItem1_id);
    }


    private static function SomeItem2sForLeveledItemTwosSql($option, $SomeItem5, $LeveledItemTwos_ids, $SomeItem9s, $volume)
    {
        $multiplier_satisfied_SomeItem9s_number = 1;

        $multiplier_SomeItem9s_diff = 1;

        $map = [
            0,
            0.1,
            0.15,
            0.20,
            0.25,
            0.30,
            0.35,
            0.40,
            0.45,
            0.50,
            1
        ];

        $SomeItem9s_on_count = 0;

        if (!empty($SomeItem9s)) {
            foreach ($SomeItem9s as $SomeItem9) {
                if ($SomeItem9['checkbox_value'] == 'on') {
                    $SomeItem9s_on_count++;
                }
            }
        }

        $SomeItem1_points_max = $SomeItem9s_on_count * ($multiplier_satisfied_SomeItem9s_number + 10 * $multiplier_SomeItem9s_diff);

        $bound = $SomeItem1_points_max * (1 - $map[(int)$volume]);


        if ($LeveledItemTwos_ids == []) {
            return '';
        }

        $LeveledItemTwos_ids_string = implode(', ', $LeveledItemTwos_ids);

        $query = DB::table('SomeItem2s')
            ->whereRaw('/*Hidden*/./*Hidden*/ IN ( '/*Hidden*/ . ' )')
            ->whereRaw('SomeItem2s.SomeItem2_offline IS NULL')
            ->whereRaw('SomeItem2s.SomeItem2_is_SomeItem4 IS NULL');

        if ($option == 'wishlist' && $SomeItem5 != null) {
            return $query->whereRaw(
                "SomeItem2s.SomeItem2_id IN ( 
                    SELECT wish_fav_SomeItem2_id FROM wish_fav WHERE /*Hidden*/ = $SomeItem5->SomeItem5_id AND wish_fav_type = 'wish' AND deleted_at IS NULL
                )"
            )->select(
                'SomeItem2_id',
                DB::raw('0 AS points')
            )->toSql();
        } elseif ($option == '/*Hidden*/' && $SomeItem5 != null) {
            return $query->whereRaw(
                "SomeItem2s.SomeItem2_id IN ( 
                    SELECT /*Hidden*/ FROM /*Hidden*/ WHERE wish_fav_SomeItem5_id = $SomeItem5->SomeItem5_id AND /*Hidden*/ = '/*Hidden*/' AND /*Hidden*/ IS NULL
                )"
            )->select(
                'SomeItem2_id',
                DB::raw('0 AS points')
            )->toSql();
        } elseif ($option == 'library' && $SomeItem5 != null) {
            return $query->select(
                'SomeItem2_id',
                DB::raw('0 AS points')
            )->toSql();
        }

        $select_raw = ['SomeItem2s.SomeItem2_id'];

        $SomeItem1_SomeItem9s = "";
        $related_with_SomeItem1_SomeItem9s = "";

        $SomeItem9s_are_set = false;

        foreach ((array)$SomeItem9s as $SomeItem9) {

            if ($SomeItem9['checkbox_value'] == 'on') {
                $id = $SomeItem9['SomeItem9_id'];

                $value = $SomeItem9['value'];

                $SomeItem1_SomeItem9s = $SomeItem1_SomeItem9s . " WHEN SomeItem2s_SomeItem9s.SomeItem2_SomeItem9_SomeItem9_id = $id THEN $value";

                $related_with_SomeItem1_SomeItem9s = $related_with_SomeItem1_SomeItem9s . " WHEN SomeItem2s_SomeItem9s.SomeItem2_SomeItem9_SomeItem9_id = $id THEN 1";

                $SomeItem9s_are_set = true;
            }

        }

        if (!$SomeItem9s_are_set) {
            return $query->select(
                'SomeItem2_id',
                DB::raw('0 AS sum_diff'),
                DB::raw('0 AS satisfied_SomeItem9s_count')
            )->toSql();
        }

        $query->leftjoin('SomeItem2s_SomeItem9s', 'SomeItem2s.SomeItem2_id', 'SomeItem2s_SomeItem9s.SomeItem2_SomeItem9_SomeItem2_id');

        $select_raw[] = DB::raw("ABS(-SomeItem2s_SomeItem9s.SomeItem2_SomeItem9_value+(CASE " . $SomeItem1_SomeItem9s . " ELSE NULL END)) AS diff");

        $query_one = $query
            ->select([
                'SomeItem2s.SomeItem2_id',
                DB::raw("
                    /*Hidden*/
                    "
                ),
            ])
            ->groupBy('SomeItem2s.SomeItem2_id')
            ->orderBy('points', 'desc')
            ->toSql();

        $query_two = "
            SELECT SomeItem2_id, points FROM ($query_one) as pre_result
            WHERE points >= $bound
        ";

        return $query_two;
    }

    // This function is static, because some lists can be obtained without SomeItem1 instance.
    // But this lists are still filtered SomeItem2s (which behaviour is similar to SomeItem1).
    public static function getSomeItem2sCountForLeveledItemTwo($option, $SomeItem5, $LeveledItemTwos_ids, $SomeItem9s, $volume)
    {
        $SomeItem2s_query = SomeItem1::SomeItem2sForLeveledItemTwosSql($option, $SomeItem5, $LeveledItemTwos_ids, $SomeItem9s, $volume);

        $count_query = "
            SELECT COUNT(result_for_count.SomeItem2_id) AS SomeItem2s_count FROM ($SomeItem2s_query) AS result_for_count
        ";

        DB::setFetchMode(PDO::FETCH_ASSOC);

        $count = DB::select(DB::raw($count_query))[0]['SomeItem2s_count'];

        return $count;
    }

    // This function is static, because some lists can be obtained without SomeItem1 instance.
    // But this lists are still filtered SomeItem2s (which behaviour is similar to SomeItem1).
    public static function getSomeItem2sForLeveledItemTwos(
        $option, $SomeItem5, $SomeItem1, $hidden,
        $SomeItem4s, $all_SomeItem4s, $LeveledItemOne_id, $LeveledItemTwos_ids,
        $SomeItem9s, $volume, $pagination, $page,
        $sorting, $sorting_map
    )
    {
        $SomeItem2s_ids = [];

        if ($option == 'SomeItem4s') {
            $SomeItem4s = SomeItem2::where('SomeItem2_SomeItem6_id', $LeveledItemOne_id)
                ->where('SomeItem2_is_SomeItem4', 1)
                ->where('SomeItem2_offline', null)
                ->where('SomeItem2_path', 'like', $SomeItem5->SomeItem5_id . '/%');

            if ($sorting_map[$sorting] == 'SomeItem2_num_pages') {
                $SomeItem4s = $SomeItem4s->orderBy($sorting_map[$sorting], 'desc');
            }

            $SomeItem4s = $SomeItem4s
                ->skip($pagination * ($page - 1))
                ->take($pagination + 1)
                ->get();

            foreach ($SomeItem4s as $SomeItem4) {
                $SomeItem2s_ids[] = $SomeItem4->SomeItem2_id;
            }
        } else {
            $hidden_query = '';
            $default_hidden_query = '';
            $SomeItem4s_query = '';

            $SomeItem2s_query = SomeItem1::SomeItem2sForLeveledItemTwosSql($option, $SomeItem5, $LeveledItemTwos_ids, $SomeItem9s, $volume);

            if (!$hidden && $SomeItem1 != null && $SomeItem2s_query != '') {
                // Important: do not to forget about deleted_at - these values are ignored without direct using
                $hidden_query = "result.SomeItem2_id NOT IN ( 
                        SELECT hide_SomeItem2_id FROM hides WHERE hide_SomeItem5_id = $SomeItem5->SomeItem5_id AND hide_SomeItem1_id = $SomeItem1->SomeItem1_id AND deleted_at IS NULL
                )";
            }

            if ($SomeItem1 != null && !in_array($SomeItem5->SomeItem5_id, Config::get('Projectconfig.default_SomeItem5s_ids')) && $SomeItem2s_query != '') {
                $iterator = 0;
                foreach (Config::get('Projectconfig.default_SomeItem5s_ids') as $default_SomeItem5_id) {
                    $iterator++;
                    if ($iterator != 1) {
                        $default_hidden_query = $default_hidden_query . " AND ";
                    }
                    $default_hidden_query = $default_hidden_query . " result.SomeItem2_id NOT IN ( 
                        SELECT hide_SomeItem2_id FROM hides WHERE hide_SomeItem5_id = $default_SomeItem5_id AND hide_SomeItem1_id = $SomeItem1->SomeItem1_id AND deleted_at IS NULL
                    )";
                }
            }

            if ($SomeItem2s_query != '') {
                $SomeItem2s_ids_query = "
                  /*Hidden*/
                  ";

                if ($hidden_query != '' || $default_hidden_query != '') {
                    $SomeItem2s_ids_query = $SomeItem2s_ids_query . " WHERE";
                    if ($hidden_query != '' && $default_hidden_query != '') {
                        $default_hidden_query = "AND " . $default_hidden_query;
                    }
                    $SomeItem2s_ids_query = $SomeItem2s_ids_query . " " . $hidden_query . " " . $default_hidden_query;
                }

                $sorting_query_part = '';

                if ($sorting_map[$sorting] != 'relevance') {
                    $sorting_query_part = " 
                        ORDER BY sort_SomeItem2s.$sorting_map[$sorting] DESC
                    ";
                }

                $SomeItem2s_ids_query = $SomeItem2s_ids_query . " " . $sorting_query_part;
            } else {
                $SomeItem2s_ids_query = '';
            }

            if ($SomeItem4s && $SomeItem1 != null) {

                if ($all_SomeItem4s) {
                    $SomeItem4s_query = SomeItem2::whereRaw("SomeItem2_path LIKE '$SomeItem5->SomeItem5_id/SomeItem4s/%'")
                        ->whereRaw("SomeItem2_is_SomeItem4 = 1")
                        ->whereRaw("SomeItem2_offline IS NULL")
                        ->whereRaw("SomeItem2_SomeItem6_id = $LeveledItemOne_id");
                } else {
                    $SomeItem4s_query = SomeItem2::whereRaw("SomeItem2_path LIKE '$SomeItem5->SomeItem5_id/SomeItem4s/$SomeItem1->SomeItem1_id'")
                        ->whereRaw("SomeItem2_is_SomeItem4 = 1")
                        ->whereRaw("SomeItem2_offline IS NULL")
                        ->whereRaw("SomeItem2_SomeItem6_id = $LeveledItemOne_id");
                }

                if ($sorting_map[$sorting] == 'SomeItem2_num_pages') {
                    $SomeItem4s_query = $SomeItem4s_query->orderBy($sorting_map[$sorting], 'desc');
                }

                $SomeItem4s_query = $SomeItem4s_query->select('SomeItem2_id', DB::raw('0 AS sum_diff'), DB::raw('0 AS satisfied_SomeItem9s_count'))
                    ->toSql();
            }

            if ($SomeItem4s_query != '' && $SomeItem2s_ids_query != '') {
                $SomeItem2s_ids_query = "
                  SELECT result.SomeItem2_id FROM ($SomeItem4s_query) AS result
                  UNION " . $SomeItem2s_ids_query;
            } elseif ($SomeItem4s_query != '' && $SomeItem2s_ids_query == '') {
                $SomeItem2s_ids_query = "
                  SELECT result.SomeItem2_id FROM ($SomeItem4s_query) AS result
                ";
            } elseif ($SomeItem4s_query == '' && $SomeItem2s_ids_query == '') {
                return ['SomeItem2s' => [], 'end_of_list' => true];
            }

            DB::setFetchMode(PDO::FETCH_ASSOC);

            $obtained_SomeItem2s_ids = DB::select(DB::raw($SomeItem2s_ids_query . ' LIMIT ' . ($pagination + 1) . ' OFFSET ' . (($page - 1) * $pagination)));

            $SomeItem2s_ids = [];

            foreach ($obtained_SomeItem2s_ids as $obtained_SomeItem2_id) {
                $SomeItem2s_ids[] = $obtained_SomeItem2_id['SomeItem2_id'];
            }

        }

        $SomeItem2s_ids_ordered = implode(',', $SomeItem2s_ids);

        if ($SomeItem2s_ids == []) {
            return ['SomeItem2s' => [], 'end_of_list' => true];
        }

        $SomeItem2s = SomeItem2::whereIn('SomeItem2_id', $SomeItem2s_ids)
            ->orderByRaw(DB::raw("FIELD(SomeItem2_id, $SomeItem2s_ids_ordered)"))
            ->with(array(
                'parentSomeItem6' => function ($query) {
                    $query->select('SomeItem6_id', 'SomeItem6_name', 'SomeItem6_display_name');
                }))
            ->get();


        $end_of_list = true;

        if ($SomeItem2s->count() > $pagination) {
            $SomeItem2s->pop();
            $end_of_list = false;
        }

        return ['SomeItem2s' => $SomeItem2s, 'end_of_list' => $end_of_list];

    }
}
