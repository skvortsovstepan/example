<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDO;
use __project__\Facades\__some__auth_service__;

class __some__item_1__ extends Model
{
    protected $primaryKey = '__some__item_1___id';

    protected $guarded = [];

    public function __some_item_9__s()
    {
        return $this->belongsToMany('App\__some_item_9__', '__some__item_1__s_meta', '__some__item_1___meta___some__item_1___id', '__some__item_1___meta___some_item_9___id')
            ->withPivot('__some__item_1___meta___some_item_9___value')
            ->withTimestamps();
    }

    public function __leveled_item_two__s()
    {
        return $this->belongsToMany('App\__some__item__6_', '__some__item_1__s_meta', '__some__item_1___meta___some__item_1___id', '__some__item_1___meta___leveled_item_two___id')
            ->where('__some__item__6__offline', null)
            ->withTimestamps();
    }

    public function __some_item_2__sCount()
    {
        $query___leveled_item_two__s = DB::table('__some__item_1__s')
            ->whereRaw('__some__item_1__s.__some__item_1___id = ' . $this->__some__item_1___id)
            ->select(
                '__some__item_1___id',
                DB::raw('(
                    SELECT COUNT(1) 
                    FROM __some_item_2__s 
                    WHERE __some_item_2__s.__some_item_2___offline IS NULL 
                        AND __some_item_2__s.__some_item_2___is___some_item_4__ IS NULL 
                        AND __some_item_2__s.__some_item_2_____some__item__6__id IN (
                            SELECT __some__item_1___meta___leveled_item_two___id 
                            FROM __some__item_1__s_meta
                            WHERE __some__item_1___meta___some__item_1___id = __some__item_1___id
                        ) 
                ) AS __some_item_2__s_number')
            );

        $query_all = DB::table('__some__item_1__s')
            ->whereRaw('__some__item_1__s.__some__item_1___id = ' . $this->__some__item_1___id)
            ->whereRaw('__some__item_1___id NOT IN (SELECT __some__item_1___meta___some__item_1___id FROM __some__item_1__s_meta WHERE __some__item_1___meta___leveled_item_two___id IS NOT NULL)')
            ->select(
                '__some__item_1___id',
                DB::raw('0 AS __some_item_2__s_number')
            )
            ->union($query___leveled_item_two__s)
            ->toSql();

        DB::setFetchMode(PDO::FETCH_ASSOC);
        $result = DB::select($query_all);

        if (!isset($result[0])) {
            return 0;
        }

        return $result[0]['__some_item_2__s_number'];
    }

    public function __leveled_item_two__sCount()
    {
        return $this->__leveled_item_two__s()->count();
    }

    public function __leveled_item_one__sCount()
    {
        // this will be easier to define through hasManyThrough,
        // but this relation does not allow sorting on MySQL side.
        // So for now the code from sorting is used here.

        $query_no___leveled_item_one__s = DB::table('__some__item_1__s')
            ->whereRaw('__some__item_1___id = ' . $this->__some__item_1___id)
            ->whereRaw('__some__item_1___id NOT IN (SELECT __some__item_1___meta___some__item_1___id FROM __some__item_1__s_meta WHERE __some__item_1___meta___leveled_item_two___id IS NOT NULL)')
            ->select(
                '__some__item_1___id',
                DB::raw('0 AS __leveled_item_one__s_number')
            )->toSql();

        $query_all = DB::table('__some__item_1__s')
            ->whereRaw('__some__item_1___id = ' . $this->__some__item_1___id)
            ->leftjoin('__some__item_1__s_meta as __some__item_1__s_meta___leveled_item_two__s', '__some__item_1__s.__some__item_1___id', '__some__item_1__s_meta___leveled_item_two__s.__some__item_1___meta___some__item_1___id')
            /*
             * Hidden Logic
             * */
            ->groupBy('__some__item_1__s.__some__item_1___id', '__some__item__6_s.__some__item__6__parent')
            ->orderBy('__some__item_1__s.__some__item_1___id', 'asc')
            ->toSql();

        $query_all = 'SELECT __some__item_1___id, COUNT(__some__item__6__parent) as __leveled_item_one__s_number 
                          FROM /*Hidden*/ 
                          GROUP BY __some__item_1___id 
                          UNION '/*Hidden*/ . '
                          ORDER BY  /*Hidden*/ DESC,  /*Hidden*/ ASC';

        DB::setFetchMode(PDO::FETCH_ASSOC);

        $result = DB::select($query_all);

        if (!isset($result[0])) {
            return 0;
        }

        return $result[0]['__leveled_item_one__s_number'];
    }

    public function __leveled_item_three__sCount()
    {
        return $this->__leveled_item_three__s()->count();
    }

    public function does__leveled_item_one__Belong(__some__item__6_ $__leveled_item_one__)
    {
        $collection = $this->__leveled_item_two__s()->where('__some__item__6__parent', $__leveled_item_one__->__some__item__6__id)->get();

        return $collection->count() >= 1 ? true : false;
    }

    public function __leveled_item_three__s()
    {
        return $this->belongsToMany('App\__some__item__6_', '__some__item_1__s_meta', '__some__item_1___meta___some__item_1___id', '__some__item_1___meta___leveled_item_three___id')
            ->where('__some__item__6_s.__some__item__6__offline', null)
            ->withPivot('__some__item_1___meta_results_volume')
            ->withTimestamps();
    }


    // $data must contain array with keys:
    // __some__item_1___name, description,
    // group=>[value, is_new],
    // __some_item_9__s => [[__some_item_9___id, value, checkbox_value],...]
    public static function buildOrUpdate($data)
    {
        $__some__item_1__s_group_to_check = null;

        if ($data['__some__item_1___id'] != null) {
            $__some__item_1__ = __some__item_1__::find($data['__some__item_1___id']);

            $__some__item_1__s_group_to_check = $__some__item_1__->__some__item_1___group;

            $__some__item_1__->clear();
        } else {
            $__some__item_1__ = new __some__item_1__();
        }


        $__some__item_1__->__some__item_1___name = $data['__some__item_1___name'];

        $__some__item_1__->__some__item_1___description = $data['description'];

        $__some__item_1__->__some__item_1___owner_id = __some__auth_service__::id();

        $group = __some__item_1__sGroup::findOrCreate($data['group']['value'], $data['group']['is_new']);

        $__some__item_1__->__some__item_1___group = empty($group) ? null : $group->__some__item_1___group_id;

        $__some__item_1__->save();

        foreach ($data['__some_item_9__s'] as $__some_item_9__) {
            if ($__some_item_9__['checkbox_value'] == 'on') {
                $__some__item_1__->__some_item_9__s()->attach($__some_item_9__['__some_item_9___id'], ['__some__item_1___meta___some_item_9___value' => $__some_item_9__['value']]);
            }
        }

        $__some__item_1___disk = Storage::disk(Config::get('__project__config.__project_____some__item_1__s_disk'));

        if (!\__some_item_2__::isDirectory(Config::get('__some_item_2__systems.disks')['__project_____some__item_1__s_disk']['root'] . "/" . $__some__item_1__->__some__item_1___id)) {
            $__some__item_1___disk->createDir($__some__item_1__->__some__item_1___id);
        }

        $__some__item_1__->saveImg($data['__some__item_1___img_names'], $data['__some__item_1___default_icon']);

        if ($__some__item_1__s_group_to_check != null) {
            $group = __some__item_1__sGroup::find($__some__item_1__s_group_to_check);

            if ($group != false) {
                $group->checkEmptiness();
            }

        }

        return $__some__item_1__;
    }

    public function saveImg($img_names, $default_icon)
    {
        $tmp_disk = Storage::disk(Config::get('__project__config.__project___tmp_disk'));

        $__some__item_1___disk = Storage::disk(Config::get('__project__config.__project_____some__item_1__s_disk'));

        if ($img_names) {
            $length = count($img_names);

            if ($length > 1) {
                $img_name_to_parse = $img_names[$length - 1];
            } else {
                $img_name_to_parse = $img_names[0];
            }

            $tmp_name = substr($img_name_to_parse, 0, strripos($img_name_to_parse, '_'));

            $tmp_name = substr($tmp_name, 0, strripos($tmp_name, '_'));

            $img_path = $this->__some__item_1___id . '/' . $tmp_name;

            $img_content = $tmp_disk->read($img_name_to_parse);

            $__some__item_1___disk->put($img_path, $img_content);

            foreach ($img_names as $img_name) {
                $tmp_disk->delete($img_name);
            }

            $this->__some__item_1___icon = $img_path;

        } else {
            if ($this->__some__item_1___icon != $default_icon && $default_icon != null) {
                $img_content = $__some__item_1___disk->read($default_icon);

                $default_name = substr($default_icon, strripos($default_icon, '/') + 1);

                $img_path = $this->__some__item_1___id . '/' . $default_name;

                $__some__item_1___disk->put($img_path, $img_content);

                $this->__some__item_1___icon = $img_path;
            }


            if ($this->__some__item_1___icon == null && $__some__item_1___disk->has(Config::get('__project__config.__some__item_1___default___some__item__6_') . '/' . Config::get('__project__config.__some__item_1___icon_default'))) {
                $img_path = $this->__some__item_1___id . '/' . Config::get('__project__config.__some__item_1___icon_default');

                $img_content = $__some__item_1___disk->read(Config::get('__project__config.__some__item_1___default___some__item__6_') . '/' . Config::get('__project__config.__some__item_1___icon_default'));

                $__some__item_1___disk->put($img_path, $img_content);

                $this->__some__item_1___icon = $img_path;
            }
        }

        $this->save();
    }


    // $data must contain array with keys:
    // id,
    // selected_items=>[[],...],
    // volume_slider
    public function attach__leveled_item_two__s($data)
    {
        // To keep in __some__item_1__ only chosen __leveled_item_three__s, the check on emptiness is needed

        $not_empty = false;

        foreach ($data['selected_items'] as $__leveled_item_one__) {
            foreach ($__leveled_item_one__ as $__leveled_item_two___id) {
                $this->__leveled_item_two__s()->attach($__leveled_item_two___id);
                $not_empty = true;
            }
        }

        if ($not_empty) {
            $this->__leveled_item_three__s()->attach($data['id'], ['__some__item_1___meta_results_volume' => $data['volume_slider']]);
        }
    }

    public function clear()
    {
        $this->__some__item_1___name = null;

        $this->__some__item_1___description = null;

        $this->__some__item_1___group = null;

        $this->save();

        $this->__some_item_9__s()->detach();

        $this->__leveled_item_two__s()->detach();

        $this->__leveled_item_three__s()->detach();
    }

    public function __some_item_8__s()
    {
        return $this->hasMany('App\__some_item_8__', '__some_item_8___rated_id')->where('__some_item_8___type', '__some__item_1__');
    }

    public function __some_item_7__s()
    {
        return $this->hasMany('App\__some_item_7__', '__some_item_7_____some_item_7__ed_id')
            ->with(array('__some__item__5_' => function ($query) {
                $query->select('__some__item__5__id', 'login', 'name_f', 'name_l');
            }))
            ->where('__some_item_7___type', '__some__item_1__');
    }

    public function approved__some_item_7__s()
    {
        return $this->hasMany('App\__some_item_7__', '__some_item_7_____some_item_7__ed_id')
            ->with(array('__some__item__5_' => function ($query) {
                $query->select('__some__item__5__id', 'login', 'name_f', 'name_l');
            }))
            ->where('__some_item_7___approved', 1)
            ->where('__some_item_7___type', '__some__item_1__');
    }

    public function average__some_item_8__()
    {
        return $this->__some_item_8__s()->avg('__some_item_8_____some_item_8__');
    }

    public function group()
    {
        return $this->belongsTo('App\__some__item_1__sGroup', '__some__item_1___group');
    }

    public function __some__item__5___some_item_8__(__some__item__5_ $__some__item__5_)
    {
        return $this->__some_item_8__s()->where('__some_item_8_____some__item__5__id', $__some__item__5_->__some__item__5__id)->first();
    }

    public function rate(__some__item__5_ $__some__item__5_, $__some_item_8__)
    {
        $__some__item__5____some_item_8__ = $this->__some__item__5___some_item_8__($__some__item__5_);

        if ($__some__item__5____some_item_8__ == null) {
            __some_item_8__::create([
                '__some_item_8___type' => '__some__item_1__',
                '__some_item_8_____some__item__5__id' => $__some__item__5_->__some__item__5__id,
                '__some_item_8_____some_item_8__' => $__some_item_8__,
                '__some_item_8___rated_id' => $this->__some__item_1___id
            ]);
        } else {
            $__some__item__5____some_item_8__->__some_item_8_____some_item_8__ = $__some_item_8__;

            $__some__item__5____some_item_8__->save();
        }
    }

    public function __some__item__5___some_item_7__(__some__item__5_ $__some__item__5_)
    {
        return $this->__some_item_7__s()->where('__some_item_7_____some__item__5__id', $__some__item__5_->__some__item__5__id)->first();
    }

    public function __some_item_7__(__some__item__5_ $__some__item__5_, $__some_item_7___content)
    {
        $__some__item__5____some_item_7__ = $this->__some__item__5___some_item_7__($__some__item__5_);

        if ($__some__item__5____some_item_7__ == null && $__some_item_7___content != '') {
            $__some__item__5____some_item_7__ = __some_item_7__::create([
                '__some_item_7___type' => '__some__item_1__',
                '__some_item_7_____some__item__5__id' => $__some__item__5_->__some__item__5__id,
                '__some_item_7___content' => $__some_item_7___content,
                '__some_item_7_____some_item_7__ed_id' => $this->__some__item_1___id,
                '__some_item_7___approved' => null
            ]);

            if (__some__auth_service__::checkEditorPermission()) {
                $__some__item__5____some_item_7__->approve();
            }
        } elseif ($__some__item__5____some_item_7__ != null) {
            $__some__item__5____some_item_7__->__some_item_7___content = $__some_item_7___content;

            $__some__item__5____some_item_7__->__some_item_7___approved = null;

            if ($__some_item_7___content != '') {
                if (__some__auth_service__::checkEditorPermission()) {
                    $__some__item__5____some_item_7__->approve();
                } else {
                    $__some__item__5____some_item_7__->save();
                }
            } else {
                $__some__item__5____some_item_7__->delete();
            }
        }
    }

    public function img()
    {
        if (!$this->__some__item_1___icon) {
            return null;
        }

        $__some__item_1___disk = Storage::disk(\Config::get('__project__config.__project_____some__item_1__s_disk'));
        return $__some__item_1___disk->read($this->__some__item_1___icon);
    }

    private static function sqlQueryToSort($type = 'default', $sort_option = 'default', $group_id = null, __some__item__5_ $__some__item__5_ = null)
    {

        if (empty($sort_option)) {
            $sort_option = 'default';
        }

        if (!in_array($type, ['default', '__some__item__5_']) || empty($__some__item__5_)) {
            $type = 'default';
        }

        $__some__item__5_s_ids = '';

        if ($type == 'default') {
            foreach (Config::get('__project__config.default___some__item__5_s_ids') as $default___some__item__5_s_id) {
                $__some__item__5_s_ids = $__some__item__5_s_ids . ' ' . $default___some__item__5_s_id . ',';
            }
            $__some__item__5_s_ids = '( ' . substr($__some__item__5_s_ids, 0, -1) . ' )';
        } else {
            $__some__item__5_s_ids = '(' . $__some__item__5_->__some__item__5__id . ' )';
        }

        if ($sort_option == '__some_item_2__s_number') {

            $query___leveled_item_two__s = DB::table('__some__item_1__s')
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                //->whereRaw('__some__item_1___id NOT IN (SELECT __some__item_1___meta___some__item_1___id FROM __some__item_1__s_meta WHERE __some__item_1___meta___some_item_9___id IS NOT NULL)')
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('__some__item_1___group = ' . $group_id);
                })
                ->select(
                    '__some__item_1___id',
                    DB::raw('(
                    SELECT COUNT(1) 
                    FROM __some_item_2__s 
                    WHERE __some_item_2__s.__some_item_2___offline IS NULL 
                        /*Hidden*/ IN (
                            SELECT /*Hidden*/ 
                            FROM __some__item_1__s_meta
                            WHERE /*Hidden*/
                        ) 
                ) AS __some_item_2__s_number')
                );

            $query_all = DB::table('__some__item_1__s')
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                ->whereRaw('__some__item_1___id NOT IN (SELECT __some__item_1___meta___some__item_1___id FROM __some__item_1__s_meta WHERE __some__item_1___meta___leveled_item_two___id IS NOT NULL)')
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('__some__item_1___group = ' . $group_id);
                })
                ->select(
                    '__some__item_1___id',
                    DB::raw('0 AS __some_item_2__s_number')
                )
                ->union($query___leveled_item_two__s)
                ->orderBy('__some_item_2__s_number', 'desc')
                ->orderBy('__some__item_1___id', 'asc')
                ->toSql();
        } elseif ($sort_option == 'average___some_item_8__') {
            $query_no___some_item_7__s = DB::table('__some__item_1__s')
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('__some__item_1___group = ' . $group_id);
                })
                ->whereRaw('__some__item_1___id NOT IN (SELECT /*Hidden*/)')
                ->select(
                    '__some__item_1___id',
                    DB::raw('0 AS average___some_item_8__')
                );

            $query_all = DB::table('__some__item_1__s')
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('__some__item_1___group = ' . $group_id);
                })
                ->leftjoin('__some_item_8__s', '__some_item_8__s.__some_item_8___rated_id', '__some__item_1__s.__some__item_1___id')
                ->whereRaw('__some_item_8__s.__some_item_8___type = "__some__item_1__"')
                ->select(
                    /*Hidden*/
                )
                ->groupBy('__some__item_1__s.__some__item_1___id')
                ->union($query_no___some_item_7__s)
                ->orderBy('average___some_item_8__', 'desc')
                ->orderBy('__some__item_1___id', 'asc')
                ->toSql();
        } elseif ($sort_option == '__leveled_item_two__s_number') {

            $query_no___leveled_item_two__s = DB::table('__some__item_1__s')
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('__some__item_1___group = ' . $group_id);
                })
                ->whereRaw('__some__item_1___id NOT IN (SELECT __some__item_1___meta___some__item_1___id FROM __some__item_1__s_meta WHERE __some__item_1___meta___leveled_item_two___id IS NOT NULL)')
                ->select(
                    '__some__item_1___id',
                    DB::raw('0 AS __leveled_item_two__s_number')
                );

            $query_all = DB::table('__some__item_1__s')
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw(/*Hidden*/);
                })
                ->leftjoin('__some__item_1__s_meta as __some__item_1__s_meta___leveled_item_two__s', '__some__item_1__s.__some__item_1___id', '__some__item_1__s_meta___leveled_item_two__s.__some__item_1___meta___some__item_1___id')
                /*Hidden*/
                ->leftjoin('__some__item__6_s', '__some__item_1__s_meta___leveled_item_two__s.__some__item_1___meta___leveled_item_two___id', '__some__item__6_s.__some__item__6__id')
                /*Hidden*/
                ->select(
                    '__some__item_1__s.__some__item_1___id',
                    DB::raw('COUNT(/*Hidden*/) AS __leveled_item_two__s_number')
                )
                ->groupBy('__some__item_1__s.__some__item_1___id')
                ->union($query_no___leveled_item_two__s)
                ->orderBy('__leveled_item_two__s_number', 'desc')
                ->orderBy('__some__item_1___id', 'asc')
                ->toSql();

        } elseif ($sort_option == '__leveled_item_one__s_number') {

            $query_no___leveled_item_one__s = DB::table('__some__item_1__s')
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('__some__item_1___group = ' . $group_id);
                })
                ->whereRaw('__some__item_1___id NOT IN (SELECT /*Hidden*/ FROM /*Hidden*/ WHERE /*Hidden*/ IS NOT NULL)')
                ->select(
                    '__some__item_1___id',
                    DB::raw('0 AS __leveled_item_one__s_number')
                )->toSql();

            $query_all = DB::table('__some__item_1__s')
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('__some__item_1___group = ' . $group_id);
                })
                ->leftjoin(/*Hidden*/)
                ->where('/*Hidden*/', '<>', null)
                ->leftjoin('__some__item__6_s', '__some__item__6_s.__some__item__6__id', '__some__item_1__s_meta___leveled_item_two__s.__some__item_1___meta___leveled_item_two___id')
                ->where('__some__item__6_s.__some__item__6__offline', null)
                ->where('/*Hidden*/', '<>', null)
                ->select(
                    '__some__item_1__s.__some__item_1___id',
                    '/*Hidden*/'
                )
                ->groupBy('__some__item_1__s.__some__item_1___id', '__some__item__6_s.__some__item__6__parent')
                ->orderBy('__some__item_1__s.__some__item_1___id', 'asc')
                ->toSql();

            $query_all = 'SELECT /*Hidden*/
                          FROM (' /*Hidden*/ . ') as result 
                          GROUP BY __some__item_1___id 
                          UNION ' /*Hidden*/ . '
                          ORDER BY __leveled_item_one__s_number DESC, __some__item_1___id ASC';

        } elseif ($sort_option == '__leveled_item_three__s_number') {

            $query_no___leveled_item_three__s = DB::table('__some__item_1__s')
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('__some__item_1___group = ' . $group_id);
                })
                ->whereRaw('__some__item_1___id NOT IN (SELECT /*Hidden*/ FROM /*Hidden*/ WHERE /*Hidden*/ IS NOT NULL)')
                ->select(
                    '__some__item_1___id',
                    DB::raw('0 AS /*Hidden*/')
                );

            $query_all = DB::table('__some__item_1__s')
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('__some__item_1___group = ' . $group_id);
                })
                ->leftjoin('/*Hidden*/ as /*Hidden*/', '/*Hidden*/./*Hidden*/', '/*Hidden*/./*Hidden*/')
                ->where('/*Hidden*/./*Hidden*/', '<>', null)
                ->leftjoin('__some__item__6_s', '/*Hidden*/./*Hidden*/', '__some__item__6_s.__some__item__6__id')
                ->where('__some__item__6_s.__some__item__6__offline', null)
                ->select(
                    '__some__item_1__s.__some__item_1___id',
                    DB::raw('COUNT(/*Hidden*/) AS __leveled_item_three__s_number')
                )
                ->groupBy('__some__item_1__s.__some__item_1___id')
                ->union($query_no___leveled_item_three__s)
                ->orderBy('__leveled_item_three__s_number', 'desc')
                ->orderBy('__some__item_1___id', 'asc')
                ->toSql();
        } elseif ($sort_option == '__some_item_7__s_number') {
            $query_no___some_item_7__s = DB::table('__some__item_1__s')
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('__some__item_1___group = ' . $group_id);
                })
                ->whereRaw('/*Hidden*/ NOT IN (SELECT /*Hidden*/ FROM /*Hidden*/ WHERE /*Hidden*/ = "/*Hidden*/")')
                ->select(
                    '__some__item_1___id',
                    DB::raw('0 AS __some_item_7__s_number')
                );

            $query_all = DB::table('__some__item_1__s')
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('__some__item_1___group = ' . $group_id);
                })
                ->leftjoin('__some_item_7__s', '/*Hidden*/', '__some__item_1__s.__some__item_1___id')
                ->whereRaw('__some_item_7__s.__some_item_7___type = "__some__item_1__"')
                ->select(
                    '__some__item_1__s.__some__item_1___id',
                    DB::raw('SUM(CASE WHEN /*Hidden*/ IS NULL THEN 0 ELSE 1 END) AS /*Hidden*/')
                )
                ->groupBy('__some__item_1__s.__some__item_1___id')
                ->union($query_no___some_item_7__s)
                ->orderBy('__some_item_7__s_number', 'desc')
                ->orderBy('__some__item_1___id', 'asc')
                ->toSql();
        } //elseif($sort_option == 'relevancy'){}

        else {
            $query_all = __some__item_1__::select(
                '__some__item_1___id',
                DB::raw('NULL as "' . $sort_option . '"')
            )
                ->whereRaw('__some__item_1___owner_id IN ' . $__some__item__5_s_ids)
                ->when(!empty($group_id), function ($query) use ($group_id) {
                    return $query->whereRaw('__some__item_1___group = ' . $group_id);
                })
                ->toSql();
        }

        return $query_all;
    }

    public static function sortAndPaginate($type, $sort_option, $group_id, $page, $pagination, $__some__item__5_)
    {
        $query = static::sqlQueryToSort($type, $sort_option, $group_id, $__some__item__5_) . ' LIMIT ' . ($pagination + 1) . ' OFFSET ' . (($page - 1) * $pagination);

        DB::setFetchMode(PDO::FETCH_ASSOC);

        $results_raw = DB::select($query);

        $end_of_list = true;

        $rows_number = count($results_raw);

        if ($rows_number > $pagination) {
            $end_of_list = false;
            unset($results_raw[$rows_number - 1]);
        }

        $__some__item_1__s = [];

        foreach ($results_raw as $__some__item_1___raw) {
            $__some__item_1__ = __some__item_1__::with('group')->find($__some__item_1___raw['__some__item_1___id']);

            $__some__item_1__s[] = [
                '__some__item_1__' => $__some__item_1__,
                '__some__item_1_____some_item_9__s' => $__some__item_1__->__some_item_9__s()->get(),
                '__some__item_1___average___some_item_8__' => $sort_option == 'average___some_item_8__' ? $__some__item_1___raw['average___some_item_8__'] : $__some__item_1__->average__some_item_8__(),
                '__some__item_1___number_votes' => $__some__item_1__->__some_item_8__s()->count(),
                '__some__item_1___number___some_item_7__s' => $sort_option == '__some_item_7__s_number' ? $__some__item_1___raw['__some_item_7__s_number'] : $__some__item_1__->approved__some_item_7__s()->count(),
                '__some__item_1___number___some_item_2__s' => $sort_option == '__some_item_2__s_number' ? $__some__item_1___raw['__some_item_2__s_number'] : $__some__item_1__->__some_item_2__sCount(),
                '__some__item_1___number___leveled_item_two__s' => $sort_option == '__leveled_item_two__s_number' ? $__some__item_1___raw['__leveled_item_two__s_number'] : $__some__item_1__->__leveled_item_two__sCount(),
                '__some__item_1___number___leveled_item_one__s' => $sort_option == '__leveled_item_one__s_number' ? $__some__item_1___raw['__leveled_item_one__s_number'] : $__some__item_1__->__leveled_item_one__sCount(),
                '__some__item_1___number___leveled_item_three__s' => $sort_option == '__leveled_item_three__s_number' ? $__some__item_1___raw['__leveled_item_three__s_number'] : $__some__item_1__->__leveled_item_three__sCount(),

            ];
        }

        return ['__some__item_1__s' => $__some__item_1__s, 'end_of_list' => $end_of_list];
    }

    public function remove__some__item_1__(__some__item__5_ $__some__item__5_)
    {
        $__some__item_1___disk = Storage::disk(Config::get('__project__config.__project_____some__item_1__s_disk'));

        if ($__some__item__5_->isDefault()) {
            __some__item__5_::chunk(100, function ($__some__item__5_s) {
                foreach ($__some__item__5_s as $__some__item__5_) {
                    $this->unbind__some__item_1__From__some__item__5_($__some__item__5_);
                }
            });
        } else {
            $this->unbind__some__item_1__From__some__item__5_($__some__item__5_);
        }

        $__some__item_1___disk->deleteDir($this->__some__item_1___id);

        DB::table('__some__item_1__s_meta')->where('__some__item_1___meta___some__item_1___id', $this->__some__item_1___id)->delete();
        DB::table('hides')->where('hide___some__item_1___id', $this->__some__item_1___id)->delete();
        $this->__some_item_7__s()->delete();
        $this->__some_item_8__s()->delete();

        $__some__item_1___group_id = $this->__some__item_1___group;

        $this->delete();

        if ($__some__item_1___group_id != null) {
            $group = __some__item_1__sGroup::find($__some__item_1___group_id);

            if ($group != false) {
                $group->checkEmptiness();
            }
        }
    }

    private function unbind__some__item_1__From__some__item__5_(__some__item__5_ $__some__item__5_)
    {
        $__some__item__5_s_disk = Storage::disk(Config::get('__project__config.__project_____some__item__5_s_disk'));

        foreach ($__some__item__5_s_disk->__some_item_2__s($__some__item__5_->__some__item__5__id . '/__some_item_4__s/' . $this->__some__item_1___id) as $__some_item_2__) {
            $__some_item_2___name = substr($__some_item_2__, strripos($__some_item_2__, '/') + 1);

            $__some_item_4__ = __some_item_2__::where('__some_item_2___name', $__some_item_2___name)->where('__some_item_2___is___some_item_4__', 1)->first();

            if ($__some_item_4__ == false) {
                continue;
            }

            if ($__some__item__5_s_disk->has($__some__item__5_->__some__item__5__id . '/__some_item_4__s/Unrelated/' . $__some_item_2___name)) {
                $i = 1;
                $__some_item_2___name_short = substr($__some_item_2___name, 0, strripos($__some_item_2___name, '.pdf'));
                while ($__some__item__5_s_disk->has($__some__item__5_->__some__item__5__id . '/__some_item_4__s/Unrelated/' . $__some_item_2___name_short . '(' . $i . ').pdf')) {
                    $i++;
                }
                $__some_item_2___name = $__some_item_2___name_short . '(' . $i . ').pdf';
            }

            $__some_item_4__->__some_item_2___name = $__some_item_2___name;

            $__some_item_4__->__some_item_2___path = $__some__item__5_->__some__item__5__id . '/__some_item_4__s/Unrelated';

            $__some_item_4__->save();

            $__some__item__5_s_disk->move($__some_item_2__, $__some__item__5_->__some__item__5__id . '/__some_item_4__s/Unrelated/' . $__some_item_2___name);
        }

        $__some__item__5_s_disk->deleteDir($__some__item__5_->__some__item__5__id . '/__some_item_4__s/' . $this->__some__item_1___id);
    }


    private static function __some_item_2__sFor__leveled_item_two__sSql($option, $__some__item__5_, $__leveled_item_two__s_ids, $__some_item_9__s, $volume)
    {
        $multiplier_satisfied___some_item_9__s_number = 1;

        $multiplier___some_item_9__s_diff = 1;

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

        $__some_item_9__s_on_count = 0;

        if (!empty($__some_item_9__s)) {
            foreach ($__some_item_9__s as $__some_item_9__) {
                if ($__some_item_9__['checkbox_value'] == 'on') {
                    $__some_item_9__s_on_count++;
                }
            }
        }

        $__some__item_1___points_max = $__some_item_9__s_on_count * ($multiplier_satisfied___some_item_9__s_number + 10 * $multiplier___some_item_9__s_diff);

        $bound = $__some__item_1___points_max * (1 - $map[(int)$volume]);


        if ($__leveled_item_two__s_ids == []) {
            return '';
        }

        $__leveled_item_two__s_ids_string = implode(', ', $__leveled_item_two__s_ids);

        $query = DB::table('__some_item_2__s')
            ->whereRaw('/*Hidden*/./*Hidden*/ IN ( '/*Hidden*/ . ' )')
            ->whereRaw('__some_item_2__s.__some_item_2___offline IS NULL')
            ->whereRaw('__some_item_2__s.__some_item_2___is___some_item_4__ IS NULL');

        if ($option == 'wishlist' && $__some__item__5_ != null) {
            return $query->whereRaw(
                "__some_item_2__s.__some_item_2___id IN ( 
                    SELECT wish_fav___some_item_2___id FROM wish_fav WHERE /*Hidden*/ = $__some__item__5_->__some__item__5__id AND wish_fav_type = 'wish' AND deleted_at IS NULL
                )"
            )->select(
                '__some_item_2___id',
                DB::raw('0 AS points')
            )->toSql();
        } elseif ($option == '/*Hidden*/' && $__some__item__5_ != null) {
            return $query->whereRaw(
                "__some_item_2__s.__some_item_2___id IN ( 
                    SELECT /*Hidden*/ FROM /*Hidden*/ WHERE wish_fav___some__item__5__id = $__some__item__5_->__some__item__5__id AND /*Hidden*/ = '/*Hidden*/' AND /*Hidden*/ IS NULL
                )"
            )->select(
                '__some_item_2___id',
                DB::raw('0 AS points')
            )->toSql();
        } elseif ($option == 'library' && $__some__item__5_ != null) {
            return $query->select(
                '__some_item_2___id',
                DB::raw('0 AS points')
            )->toSql();
        }

        $select_raw = ['__some_item_2__s.__some_item_2___id'];

        $__some__item_1_____some_item_9__s = "";
        $related_with___some__item_1_____some_item_9__s = "";

        $__some_item_9__s_are_set = false;

        foreach ((array)$__some_item_9__s as $__some_item_9__) {

            if ($__some_item_9__['checkbox_value'] == 'on') {
                $id = $__some_item_9__['__some_item_9___id'];

                $value = $__some_item_9__['value'];

                $__some__item_1_____some_item_9__s = $__some__item_1_____some_item_9__s . " WHEN __some_item_2__s___some_item_9__s.__some_item_2_____some_item_9_____some_item_9___id = $id THEN $value";

                $related_with___some__item_1_____some_item_9__s = $related_with___some__item_1_____some_item_9__s . " WHEN __some_item_2__s___some_item_9__s.__some_item_2_____some_item_9_____some_item_9___id = $id THEN 1";

                $__some_item_9__s_are_set = true;
            }

        }

        if (!$__some_item_9__s_are_set) {
            return $query->select(
                '__some_item_2___id',
                DB::raw('0 AS sum_diff'),
                DB::raw('0 AS satisfied___some_item_9__s_count')
            )->toSql();
        }

        $query->leftjoin('__some_item_2__s___some_item_9__s', '__some_item_2__s.__some_item_2___id', '__some_item_2__s___some_item_9__s.__some_item_2_____some_item_9_____some_item_2___id');

        $select_raw[] = DB::raw("ABS(-__some_item_2__s___some_item_9__s.__some_item_2_____some_item_9___value+(CASE " . $__some__item_1_____some_item_9__s . " ELSE NULL END)) AS diff");

        $query_one = $query
            ->select([
                '__some_item_2__s.__some_item_2___id',
                DB::raw("
                    /*Hidden*/
                    "
                ),
            ])
            ->groupBy('__some_item_2__s.__some_item_2___id')
            ->orderBy('points', 'desc')
            ->toSql();

        $query_two = "
            SELECT __some_item_2___id, points FROM ($query_one) as pre_result
            WHERE points >= $bound
        ";

        return $query_two;
    }

    // This function is static, because some lists can be obtained without __some__item_1__ instance.
    // But this lists are still filtered __some_item_2__s (which behaviour is similar to __some__item_1__).
    public static function get__some_item_2__sCountFor__leveled_item_two__($option, $__some__item__5_, $__leveled_item_two__s_ids, $__some_item_9__s, $volume)
    {
        $__some_item_2__s_query = __some__item_1__::__some_item_2__sFor__leveled_item_two__sSql($option, $__some__item__5_, $__leveled_item_two__s_ids, $__some_item_9__s, $volume);

        $count_query = "
            SELECT COUNT(result_for_count.__some_item_2___id) AS __some_item_2__s_count FROM ($__some_item_2__s_query) AS result_for_count
        ";

        DB::setFetchMode(PDO::FETCH_ASSOC);

        $count = DB::select(DB::raw($count_query))[0]['__some_item_2__s_count'];

        return $count;
    }

    // This function is static, because some lists can be obtained without __some__item_1__ instance.
    // But this lists are still filtered __some_item_2__s (which behaviour is similar to __some__item_1__).
    public static function get__some_item_2__sFor__leveled_item_two__s(
        $option, $__some__item__5_, $__some__item_1__, $hidden,
        $__some_item_4__s, $all___some_item_4__s, $__leveled_item_one___id, $__leveled_item_two__s_ids,
        $__some_item_9__s, $volume, $pagination, $page,
        $sorting, $sorting_map
    )
    {
        $__some_item_2__s_ids = [];

        if ($option == '__some_item_4__s') {
            $__some_item_4__s = __some_item_2__::where('__some_item_2_____some__item__6__id', $__leveled_item_one___id)
                ->where('__some_item_2___is___some_item_4__', 1)
                ->where('__some_item_2___offline', null)
                ->where('__some_item_2___path', 'like', $__some__item__5_->__some__item__5__id . '/%');

            if ($sorting_map[$sorting] == '__some_item_2___num_pages') {
                $__some_item_4__s = $__some_item_4__s->orderBy($sorting_map[$sorting], 'desc');
            }

            $__some_item_4__s = $__some_item_4__s
                ->skip($pagination * ($page - 1))
                ->take($pagination + 1)
                ->get();

            foreach ($__some_item_4__s as $__some_item_4__) {
                $__some_item_2__s_ids[] = $__some_item_4__->__some_item_2___id;
            }
        } else {
            $hidden_query = '';
            $default_hidden_query = '';
            $__some_item_4__s_query = '';

            $__some_item_2__s_query = __some__item_1__::__some_item_2__sFor__leveled_item_two__sSql($option, $__some__item__5_, $__leveled_item_two__s_ids, $__some_item_9__s, $volume);

            if (!$hidden && $__some__item_1__ != null && $__some_item_2__s_query != '') {
                // Important: do not to forget about deleted_at - these values are ignored without direct using
                $hidden_query = "result.__some_item_2___id NOT IN ( 
                        SELECT hide___some_item_2___id FROM hides WHERE hide___some__item__5__id = $__some__item__5_->__some__item__5__id AND hide___some__item_1___id = $__some__item_1__->__some__item_1___id AND deleted_at IS NULL
                )";
            }

            if ($__some__item_1__ != null && !in_array($__some__item__5_->__some__item__5__id, Config::get('__project__config.default___some__item__5_s_ids')) && $__some_item_2__s_query != '') {
                $iterator = 0;
                foreach (Config::get('__project__config.default___some__item__5_s_ids') as $default___some__item__5__id) {
                    $iterator++;
                    if ($iterator != 1) {
                        $default_hidden_query = $default_hidden_query . " AND ";
                    }
                    $default_hidden_query = $default_hidden_query . " result.__some_item_2___id NOT IN ( 
                        SELECT hide___some_item_2___id FROM hides WHERE hide___some__item__5__id = $default___some__item__5__id AND hide___some__item_1___id = $__some__item_1__->__some__item_1___id AND deleted_at IS NULL
                    )";
                }
            }

            if ($__some_item_2__s_query != '') {
                $__some_item_2__s_ids_query = "
                  /*Hidden*/
                  ";

                if ($hidden_query != '' || $default_hidden_query != '') {
                    $__some_item_2__s_ids_query = $__some_item_2__s_ids_query . " WHERE";
                    if ($hidden_query != '' && $default_hidden_query != '') {
                        $default_hidden_query = "AND " . $default_hidden_query;
                    }
                    $__some_item_2__s_ids_query = $__some_item_2__s_ids_query . " " . $hidden_query . " " . $default_hidden_query;
                }

                $sorting_query_part = '';

                if ($sorting_map[$sorting] != 'relevance') {
                    $sorting_query_part = " 
                        ORDER BY sort___some_item_2__s.$sorting_map[$sorting] DESC
                    ";
                }

                $__some_item_2__s_ids_query = $__some_item_2__s_ids_query . " " . $sorting_query_part;
            } else {
                $__some_item_2__s_ids_query = '';
            }

            if ($__some_item_4__s && $__some__item_1__ != null) {

                if ($all___some_item_4__s) {
                    $__some_item_4__s_query = __some_item_2__::whereRaw("__some_item_2___path LIKE '$__some__item__5_->__some__item__5__id/__some_item_4__s/%'")
                        ->whereRaw("__some_item_2___is___some_item_4__ = 1")
                        ->whereRaw("__some_item_2___offline IS NULL")
                        ->whereRaw("__some_item_2_____some__item__6__id = $__leveled_item_one___id");
                } else {
                    $__some_item_4__s_query = __some_item_2__::whereRaw("__some_item_2___path LIKE '$__some__item__5_->__some__item__5__id/__some_item_4__s/$__some__item_1__->__some__item_1___id'")
                        ->whereRaw("__some_item_2___is___some_item_4__ = 1")
                        ->whereRaw("__some_item_2___offline IS NULL")
                        ->whereRaw("__some_item_2_____some__item__6__id = $__leveled_item_one___id");
                }

                if ($sorting_map[$sorting] == '__some_item_2___num_pages') {
                    $__some_item_4__s_query = $__some_item_4__s_query->orderBy($sorting_map[$sorting], 'desc');
                }

                $__some_item_4__s_query = $__some_item_4__s_query->select('__some_item_2___id', DB::raw('0 AS sum_diff'), DB::raw('0 AS satisfied___some_item_9__s_count'))
                    ->toSql();
            }

            if ($__some_item_4__s_query != '' && $__some_item_2__s_ids_query != '') {
                $__some_item_2__s_ids_query = "
                  SELECT result.__some_item_2___id FROM ($__some_item_4__s_query) AS result
                  UNION " . $__some_item_2__s_ids_query;
            } elseif ($__some_item_4__s_query != '' && $__some_item_2__s_ids_query == '') {
                $__some_item_2__s_ids_query = "
                  SELECT result.__some_item_2___id FROM ($__some_item_4__s_query) AS result
                ";
            } elseif ($__some_item_4__s_query == '' && $__some_item_2__s_ids_query == '') {
                return ['__some_item_2__s' => [], 'end_of_list' => true];
            }

            DB::setFetchMode(PDO::FETCH_ASSOC);

            $obtained___some_item_2__s_ids = DB::select(DB::raw($__some_item_2__s_ids_query . ' LIMIT ' . ($pagination + 1) . ' OFFSET ' . (($page - 1) * $pagination)));

            $__some_item_2__s_ids = [];

            foreach ($obtained___some_item_2__s_ids as $obtained___some_item_2___id) {
                $__some_item_2__s_ids[] = $obtained___some_item_2___id['__some_item_2___id'];
            }

        }

        $__some_item_2__s_ids_ordered = implode(',', $__some_item_2__s_ids);

        if ($__some_item_2__s_ids == []) {
            return ['__some_item_2__s' => [], 'end_of_list' => true];
        }

        $__some_item_2__s = __some_item_2__::whereIn('__some_item_2___id', $__some_item_2__s_ids)
            ->orderByRaw(DB::raw("FIELD(__some_item_2___id, $__some_item_2__s_ids_ordered)"))
            ->with(array(
                'parent__some__item__6_' => function ($query) {
                    $query->select('__some__item__6__id', '__some__item__6__name', '__some__item__6__display_name');
                }))
            ->get();


        $end_of_list = true;

        if ($__some_item_2__s->count() > $pagination) {
            $__some_item_2__s->pop();
            $end_of_list = false;
        }

        return ['__some_item_2__s' => $__some_item_2__s, 'end_of_list' => $end_of_list];

    }
}
