<?php

namespace App;

use FPDI;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use m1r1k\SejdaConsole\Sejda;
use __project__\Facades\__some__auth_service__;
use __project__\Sync\__some_item_2__ as __some_item_2__Interface;

class __some_item_2__ extends Model implements __some_item_2__Interface
{
    protected $primaryKey = '__some_item_2___id';

    protected $guarded = [];

    public function parent__some__item__6_()
    {
        return $this->belongsTo('App\__some__item__6_', '__some_item_2_____some__item__6__id');
        // TODO: Implement getParent__some__item__6_() method.
    }

    public function __leveled_item_two__(){
        return $this->belongsTo('App\__some__item__6_', '__some_item_2_____some__item__6__id');
    }

    public function __leveled_item_one__(){
        return $this->__leveled_item_two__()->first()->parent();
    }

    public function __leveled_item_three__(){
        return $this->__leveled_item_one__()->first()->parent();
    }

    public function __some_item_2__Flag()
    {
        return $this->belongsTo('App\__some_item_2__Flag', '__some_item_2___flag_reason');
    }

    public function fill__some_item_9__sForNew__some_item_2__()
    {
        foreach (__some_item_9__::all() as $__some_item_9__){
            $this->__some_item_9__s()->attach($__some_item_9__->__some_item_9___id, ['__some_item_2_____some_item_9___value'=>Config::get('__project__config.default___some_item_9___value')]);
        }

        return true;
    }

    public function hasDefaultThumbnail()
    {
        if(substr($this->__some_item_2___thumbnail, 0, strlen(Config::get('__project__config.__some_item_2___default___some__item__6_'))+1) == Config::get('__project__config.__some_item_2___default___some__item__6_')."/"){
            return true;
        }

        return false;
    }

    public function __some_item_9__s()
    {
        return $this->belongsToMany('App\__some_item_9__', '__some_item_2__s___some_item_9__s', '__some_item_2_____some_item_9_____some_item_2___id', '__some_item_2_____some_item_9_____some_item_9___id')
            ->withPivot('__some_item_2_____some_item_9___value', '__some_item_2_____some_item_9_____some__item__5_')
            ->withTimestamps();
    }

    public function check__some_item_9__($__some_item_9___id, $__some_item_9___value, $volume){
        return $this->__some_item_9__s()
            ->wherePivot('__some_item_2_____some_item_9_____some_item_9___id', $__some_item_9___id)
            ->wherePivot('__some_item_2_____some_item_9___value', '<=', $__some_item_9___value + $volume)
            ->wherePivot('__some_item_2_____some_item_9___value', '>=', $__some_item_9___value - $volume)
            ->first()==false ? false : true;
    }

    public function __some_item_8__s()
    {
        return $this->hasMany('App\__some_item_8__', '__some_item_8___rated_id')->where('__some_item_8___type', '__some_item_2__');
    }


    public function __some_item_7__s()
    {
        return $this->hasMany('App\__some_item_7__', '__some_item_7_____some_item_7__ed_id')
            ->with(array('__some__item__5_'=>function($query){
                $query->select('__some__item__5__id', 'login', 'name_f', 'name_l');
            }))
            ->where('__some_item_7___type', '__some_item_2__');
    }

    public function approved__some_item_7__s()
    {
        return $this->hasMany('App\__some_item_7__', '__some_item_7_____some_item_7__ed_id')
            ->with(array('__some__item__5_'=>function($query){
                $query->select('__some__item__5__id', 'login', 'name_f', 'name_l');
            }))
            ->where('__some_item_7___approved', 1)
            ->where('__some_item_7___type', '__some_item_2__');
    }

    public function favorites()
    {
        return $this->hasMany('App\WishFav', 'wish_fav___some_item_2___id')
            ->where('wish_fav_type', 'favorite');
    }

    public function wishes()
    {
        return $this->hasMany('App\WishFav', 'wish_fav___some_item_2___id')
            ->where('wish_fav_type', 'wish');
    }

    public function hides()
    {
        return $this->hasMany('App\Hide', 'hide___some_item_2___id');
    }

    public function isFavorite(__some__item__5_ $__some__item__5_)
    {
        if($this->favorites()->where('wish_fav___some__item__5__id', $__some__item__5_->__some__item__5__id)->first() !=null){
            return true;
        }
        return false;

    }

    public function isWish(__some__item__5_ $__some__item__5_)
    {
        if($this->wishes()->where('wish_fav___some__item__5__id', $__some__item__5_->__some__item__5__id)->first() !=null){
            return true;
        }
        return false;
    }

    public function isHidden(__some__item_1__ $__some__item_1__, __some__item__5_ $__some__item__5_)
    {
        if($this->hides()
                ->where('hide___some__item_1___id', $__some__item_1__->__some__item_1___id)
                ->where('hide___some__item__5__id', $__some__item__5_->__some__item__5__id)
                ->first() !=null
        ){
            return true;
        }
        return false;
    }

    public function toggleFavorite(__some__item__5_ $__some__item__5_)
    {
        $favorite_entry = $this->favorites()->withTrashed()->where('wish_fav___some__item__5__id', $__some__item__5_->__some__item__5__id)->first();

        if($favorite_entry == null){
            WishFav::create([
                'wish_fav___some_item_2___id' => $this->__some_item_2___id,
                'wish_fav___some__item__5__id' => $__some__item__5_->__some__item__5__id,
                'wish_fav_type' => 'favorite'
            ]);

            $this->__some_item_2___favourite_count=$this->__some_item_2___favourite_count+1;

            $this->save();
        }
        elseif($favorite_entry->deleted_at!=null){
            $this->__some_item_2___favourite_count=$this->__some_item_2___favourite_count+1;

            $this->save();

            $favorite_entry->restore();
        }
        else{
            $this->__some_item_2___favourite_count=$this->__some_item_2___favourite_count-1;

            $this->save();

            $favorite_entry->delete();
        }
    }

    public function toggleWish(__some__item__5_ $__some__item__5_)
    {
        $wish_entry = $this->wishes()->withTrashed()->where('wish_fav___some__item__5__id', $__some__item__5_->__some__item__5__id)->first();

        if($wish_entry == null){
            WishFav::create([
                'wish_fav___some_item_2___id' => $this->__some_item_2___id,
                'wish_fav___some__item__5__id' => $__some__item__5_->__some__item__5__id,
                'wish_fav_type' => 'wish'
            ]);

            $this->__some_item_2___wishlist_count=$this->__some_item_2___wishlist_count+1;

            $this->save();
        }
        elseif($wish_entry->trashed()){
            $this->__some_item_2___wishlist_count=$this->__some_item_2___wishlist_count+1;

            $this->save();

            $wish_entry->restore();
        }
        else{
            $this->__some_item_2___wishlist_count=$this->__some_item_2___wishlist_count-1;

            $this->save();

            $wish_entry->delete();
        }
    }

    public function toggleHidden(__some__item_1__ $__some__item_1__, __some__item__5_ $__some__item__5_)
    {
        $hide_entry = $this
            ->hides()
            ->withTrashed()
            ->where('hide___some__item_1___id', $__some__item_1__->__some__item_1___id)
            ->where('hide___some__item__5__id', $__some__item__5_->__some__item__5__id)
            ->first();

        if($hide_entry == null){
            Hide::create([
                'hide___some_item_2___id' => $this->__some_item_2___id,
                'hide___some__item__5__id' => $__some__item__5_->__some__item__5__id,
                'hide___some__item_1___id' => $__some__item_1__->__some__item_1___id
            ]);
        }
        elseif($hide_entry->trashed()){
            $hide_entry->restore();
        }
        else{
            $hide_entry->delete();
        }
    }

    public function __some__item__5___some_item_8__(__some__item__5_ $__some__item__5_)
    {
        return $this->__some_item_8__s()->where('__some_item_8_____some__item__5__id', $__some__item__5_->__some__item__5__id)->first();
    }

    public function rate(__some__item__5_ $__some__item__5_, $__some_item_8__)
    {
        $__some__item__5____some_item_8__ = $this->__some__item__5___some_item_8__($__some__item__5_);

        if($__some__item__5____some_item_8__==null){
            __some_item_8__::create([
                '__some_item_8___type' => '__some_item_2__',
                '__some_item_8_____some__item__5__id' => $__some__item__5_->__some__item__5__id,
                '__some_item_8_____some_item_8__' => $__some_item_8__,
                '__some_item_8___rated_id' => $this->__some_item_2___id
            ]);

            $this->__some_item_2_____some_item_8___count++;
        }
        else{
            $__some__item__5____some_item_8__->__some_item_8_____some_item_8__ = $__some_item_8__;

            $__some__item__5____some_item_8__->save();
        }

        $this->__some_item_2_____some_item_8__ = $this->__some_item_8__s()->avg('__some_item_8_____some_item_8__');

        $this->save();
    }

    public function __some__item__5___some_item_7__(__some__item__5_ $__some__item__5_)
    {
        return $this->__some_item_7__s()->where('__some_item_7_____some__item__5__id', $__some__item__5_->__some__item__5__id)->first();
    }

    public function __some_item_7__(__some__item__5_ $__some__item__5_, $__some_item_7___content)
    {
        $__some__item__5____some_item_7__ = $this->__some__item__5___some_item_7__($__some__item__5_);

        if($__some__item__5____some_item_7__==null && $__some_item_7___content!=''){
            $__some__item__5____some_item_7__ = __some_item_7__::create([
                '__some_item_7___type' => '__some_item_2__',
                '__some_item_7_____some__item__5__id' => $__some__item__5_->__some__item__5__id,
                '__some_item_7___content' => $__some_item_7___content,
                '__some_item_7_____some_item_7__ed_id' => $this->__some_item_2___id,
                '__some_item_7___approved' => null
            ]);

            if(__some__auth_service__::checkEditorPermission()){
                $__some__item__5____some_item_7__->approve();
            }

        }
        elseif($__some__item__5____some_item_7__ != null){
            $__some__item__5____some_item_7__->__some_item_7___content = $__some_item_7___content;

            $__some__item__5____some_item_7__->__some_item_7___approved = null;

            if($__some_item_7___content!=''){
                if(__some__auth_service__::checkEditorPermission()){
                    $__some__item__5____some_item_7__->approve();
                }
                else{
                    $__some__item__5____some_item_7__->save();
                }
            }
            else{
                $__some__item__5____some_item_7__->delete();

                $this->__some_item_2_____some_item_7___count--;
            }
        }

        $this->save();
    }

    public function hit()
    {
        $this->__some_item_2___hits++;

        $this->save();
    }

    public function lastEditor__some_item_8__()
    {
        return $this->__some_item_8__s()->whereIn('__some_item_8_____some__item__5__id', __some__item__5_::editorsIds())->orderBy('updated_at', 'desc')->first();
    }

    public function __some_item_4____some__item_1__()
    {
        if($this->__some_item_2___is___some_item_4__ == null){
            return null;
        }

        if(strtolower(explode('/', $this->__some_item_2___path)[2]) == 'unrelated'){
            return null;
        }

        return __some__item_1__::find(explode('/', $this->__some_item_2___path)[2]);
    }

    public function getRealPath($without_name = false)
    {
        if($this->__some_item_2___is___some_item_4__ == 1){
            $path = Config::get('__some_item_2__systems.disks')['__project_____some__item__5_s_disk']['root'];
        }
        else{
            $path = Config::get('__some_item_2__systems.disks')['__project_____some_item_2__s_disk']['root'];
        }

        if($without_name){
            return $path.'/'.$this->__some_item_2___path;
        }

        return $path.'/'.$this->__some_item_2___path.'/'.$this->__some_item_2___name;
    }


    public function getName(){
        if($this->__some_item_2___display_name == null){

            $before_extension = substr($this->__some_item_2___name, 0, strripos($this->__some_item_2___name, '.'));

            if(!$before_extension){
                $pre_name = $this->__some_item_2___name;
            }
            else{
                $pre_name = $before_extension;
            }

            return ucwords(str_replace('_', ' ', $pre_name), ' ');
        }
        return $this->__some_item_2___display_name;
    }

    public function getNameWithoutExtension()
    {
        if(!strripos($this->__some_item_2___name, '.')){
            return false;
        }

        return substr($this->__some_item_2___name, 0, strripos($this->__some_item_2___name, '.'));
    }

    public function create__some_item_2__DisplayName()
    {
        $before_extension = substr($this->__some_item_2___name, 0, strripos($this->__some_item_2___name, '.'));

        if(!$before_extension){
            $pre_name = $this->__some_item_2___name;
        }
        else{
            $pre_name = $before_extension;
        }

        $this->__some_item_2___display_name = ucwords(str_replace('_', ' ', $pre_name), ' ');

        $this->save();

        return $this->__some_item_2___display_name;
    }

    public function isPdf(){

        if($this->getExtension()=='pdf' && mime_content_type($this->getRealPath()) == 'application/pdf'){
            return true;
        }
        return false;
    }

    // Checks additional extensions, allowed to be shown in browser (except pdf)
    public function isAllowedExtension()
    {
        if(in_array($this->getExtension(), \Config::get('__project__config.allowed_extensions'))){
            return true;
        }
        return false;
    }

    public function getExtension()
    {
        if(!strripos($this->__some_item_2___name, '.')){
            return false;
        }

        return substr($this->__some_item_2___name, strripos($this->__some_item_2___name, '.')+1);
    }


    // WARNING! sejda app dependency!
    private function executeSejdaCommand($command){
        $log = [];

        $exit_code = 0;

        $sejda_result = exec($command, $log, $exit_code);

        $success = $exit_code===0 ? true : false;

        if(!$success){
            Log::alert($command);
            Log::alert($log);
            Log::alert($sejda_result);
        }

        return $success;
    }

    public function generateThumbnail($force = false)
    {
        if( $this->__some_item_2___thumbnail!=null && !$force && !$this->hasDefaultThumbnail()){
            return  false;
        }

        $path = Config::get('__some_item_2__systems.disks')['__project_____some_item_2__s_meta_disk']['root']."/".$this->__some_item_2___id;

        if(!\__some_item_2__::isDirectory($path)){
            \__some_item_2__::makeDirectory($path);
        }

        if($this->isPdf()){

            $command = Config::get('__project__config.sejda_path')
                ." pdftojpeg "
                ."-j overwrite "
                ."-f '".$this->getRealPath()."'"." "
                ." -s 1"
                ." -o "
                ."'".$path."'"
                ." -r 72";

            $result = $this->executeSejdaCommand($command);

            if($result){

                // Sejda saves with 1_ prefix, that is needed to be removed. If __some_item_2__ is already exists, it wil be overwritten
                \__some_item_2__::move($path."/1_".$this->getNameWithoutExtension().".jpg", $path."/".$this->getNameWithoutExtension().".jpg");

                $this->__some_item_2___thumbnail = $this->__some_item_2___id."/".$this->getNameWithoutExtension().".jpg";

                $this->save();

                return true;
            }

        }

        $default___some_item_2__s = Storage::disk('__project_____some_item_2__s_meta_disk')->__some_item_2__s(Config::get('__project__config.__some_item_2___default___some__item__6_'));

        $thumbnail_name = Config::get('__project__config.__some_item_2___default___some__item__6_')."/".Config::get('__project__config.__some_item_2___thumbnail_default');

        foreach ($default___some_item_2__s as $default___some_item_2__){
            $default_name = substr(substr($default___some_item_2__, 0, strripos($default___some_item_2__, '.')), strripos($default___some_item_2__, '/')+1);

            if ($default_name == $this->getExtension()){
                $thumbnail_name = $default___some_item_2__;
            }
        }

        $this->__some_item_2___thumbnail = $thumbnail_name;

        $this->save();

        return true;
    }

    public function extractPagesFrom__some_item_2__(__some_item_2__ $__some_item_2__, $start = null, $end = null, $all = false)
    {
        if($this->__some_item_2___is___some_item_4__ !==1){
            return false;
        }

        if($all){
            $selection = "all";
        }
        else{
            $selection = $start."-".$end;
        }

        $command = Config::get('__project__config.sejda_path')
            ." merge -f "
            ."'".$this->getRealPath()."'"." "
            ."'".$__some_item_2__->getRealPath()."'"
            ." -s all:"
            .$selection.":"
            ." -o "
            ."'".$this->getRealPath()."'"
            ." --overwrite";

        $header_page_number = $this->getRealNumberOfPages();

        if($header_page_number == null){
            return false;
        }

        ++$header_page_number;

        $merge_result = $this->executeSejdaCommand($command);

        if($merge_result){
            $this->writeSpecialHeader($__some_item_2__->getName(), $header_page_number);

            $this->__some_item_2___num_pages = $this->getRealNumberOfPages();

            $this->__some_item_2___size = $this->getReal__some_item_2__Size();

            $__some_item_2__->__some_item_2___extraction_hits++;

            $__some_item_2__->save();

            $this->save();

            return true;
        }

        return false;

    }

    public function createNew__some_item_4__(__some_item_2__ $__some_item_2__, $start, $end, $all=false){
        if($all){
            $selection = "all:";
        }
        else{
            $selection = $start."-".$end;
        }

        $command = Config::get('__project__config.sejda_path')
            ." merge -f "
            ."'".$__some_item_2__->getRealPath()."'"
            ." -s ".$selection.":"
            ." -o "
            ."'".$this->getRealPath()."'"
            ." --overwrite";

        $header_page_number = 1;

        $merge_result = $this->executeSejdaCommand($command);

        if($merge_result){
            $this->writeSpecialHeader($__some_item_2__->getName(), $header_page_number);

            $this->__some_item_2___num_pages = $this->getRealNumberOfPages();

            $this->__some_item_2___size = $this->getReal__some_item_2__Size();

            $__some_item_2__->__some_item_2___extraction_hits++;

            $__some_item_2__->save();

            $this->save();

            $this->generateThumbnail();

            return true;
        }

        $this->delete();

        return false;
    }

    private function writeSpecialHeader($header, $page_number)
    {
        if(!$page_number || !$header){
            return false;
        }


        if($this->__some_item_2___is___some_item_4__ !==1){
            return false;
        }

        $command = Config::get('__project__config.sejda_path')
            ." setheaderfooter -j overwrite -o "
            ."'".$this->getRealPath(true)."'"
            ." -f "
            ."'".$this->getRealPath()."'"
            ." -t  Helvetica-Oblique  -l "
            ."'".$header."'"
            ." -s ".$page_number."-".$page_number
            ." -y top -x right -c  '#0000FF' -d 8 ";

        return $this->executeSejdaCommand($command);
    }

    // WARNING! pdfinfo app dependency!
    public function meta()
    {
        if(!$this->isPdf()){
            return null;
        }

        $log='';

        $exit_code = 0;

        $command = Config::get('__project__config.pdfinfo_path')
            ." -meta "
            ."'".$this->getRealPath()."'";

        $pdfinfo_result = exec($command, $log, $exit_code);

        if($exit_code === 0){
            return $log;
        }
        else{
            return null;
        }

    }

    public function getRealNumberOfPages()
    {
        $meta = $this->meta();
        if($meta == null){
            return null;
        }

        foreach ($this->meta() as $item){
            if(strtolower(substr($item, 0, 6))=='pages:'){
                return trim(substr($item, stripos($item, ":")+1));
            }
        }

        return null;
    }

    public function getReal__some_item_2__Size()
    {
        return __some_item_2__size($this->getRealPath());
    }


    public function lock(__some__item__5_ $__some__item__5_){
        $this->__some_item_2_____some_item_9__s_locked_by = $__some__item__5_->__some__item__5__id;

        $this->__some_item_2_____some_item_9__s_lock = date("Y-m-d H:i:s");

        $this->save();
    }

    public function unlock(){
        $this->__some_item_2_____some_item_9__s_locked_by = null;

        $this->__some_item_2_____some_item_9__s_lock = null;

        $this->save();
    }

    public static function updateAllThumbnails($force = false)
    {
        __some_item_2__::chunk(100, function($__some_item_2__s) use ($force) {
            foreach ($__some_item_2__s as $__some_item_2__){
                $__some_item_2__->generateThumbnail(false); // Aways $force == false
            }
        });

        return true;
    }

    public function thumbnail()
    {
        if(!$this->__some_item_2___thumbnail){
            return null;
        }

        $__some_item_2__s_meta_disk = Storage::disk(\Config::get('__project__config.__project_____some_item_2__s_meta_disk'));
        return $__some_item_2__s_meta_disk->read($this->__some_item_2___thumbnail);
    }

    public function delete__some_item_4__(){
        if($this->__some_item_2___is___some_item_4__ != 1){
            return false;
        }

        $__some_item_2__s_meta_disk = Storage::disk(\Config::get('__project__config.__project_____some_item_2__s_meta_disk'));

        $__some_item_2__s_meta_disk->deleteDir($this->__some_item_2___id);

        $__some_item_2__s_meta_disk = Storage::disk(\Config::get('__project__config.__project_____some__item__5_s_disk'));

        $__some_item_2__s_meta_disk->delete($this->__some_item_2___path."/".$this->__some_item_2___name);

        $this->delete();

        return true;
    }

    public function delete__some_item_2__()
    {
        $this->__some_item_9__s()->detach();
        $this->__some_item_7__s()->delete();
        $this->wishes()->forceDelete();
        $this->favorites()->forceDelete();
        $this->__some_item_8__s()->delete();
        $this->hides()->forceDelete();
        $this->delete();

        $__some_item_2__s_meta_disk = Storage::disk(\Config::get('__project__config.__project_____some_item_2__s_meta_disk'));

        $__some_item_2__s_meta_disk->deleteDir($this->__some_item_2___id);
    }

    public function related__some__item_1__()
    {
        if($this->__some_item_2___is___some_item_4__ != 1){
            return null;
        }

        $parts = explode('/', $this->__some_item_2___path);

        $__some__item_1___id = $parts[count($parts)-1];

        if(is_numeric($__some__item_1___id)){
            return __some__item_1__::find($__some__item_1___id)->__some__item_1___name;
        }

        return $__some__item_1___id;
    }

}
