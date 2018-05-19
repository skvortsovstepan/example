<?php

namespace App;

use FPDI;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use m1r1k\SejdaConsole\Sejda;
use Project\Facades\SomeAuthService;
use Project\Sync\SomeItem2 as SomeItem2Interface;

class SomeItem2 extends Model implements SomeItem2Interface
{
    protected $primaryKey = 'SomeItem2_id';

    protected $guarded = [];

    public function parentSomeItem6()
    {
        return $this->belongsTo('App\SomeItem6', 'SomeItem2_SomeItem6_id');
        // TODO: Implement getParentSomeItem6() method.
    }

    public function LeveledItemTwo(){
        return $this->belongsTo('App\SomeItem6', 'SomeItem2_SomeItem6_id');
    }

    public function LeveledItemOne(){
        return $this->LeveledItemTwo()->first()->parent();
    }

    public function LeveledItemThree(){
        return $this->LeveledItemOne()->first()->parent();
    }

    public function SomeItem2Flag()
    {
        return $this->belongsTo('App\SomeItem2Flag', 'SomeItem2_flag_reason');
    }

    public function fillSomeItem9sForNewSomeItem2()
    {
        foreach (SomeItem9::all() as $SomeItem9){
            $this->SomeItem9s()->attach($SomeItem9->SomeItem9_id, ['SomeItem2_SomeItem9_value'=>Config::get('Projectconfig.default_SomeItem9_value')]);
        }

        return true;
    }

    public function hasDefaultThumbnail()
    {
        if(substr($this->SomeItem2_thumbnail, 0, strlen(Config::get('Projectconfig.SomeItem2_default_SomeItem6'))+1) == Config::get('Projectconfig.SomeItem2_default_SomeItem6')."/"){
            return true;
        }

        return false;
    }

    public function SomeItem9s()
    {
        return $this->belongsToMany('App\SomeItem9', 'SomeItem2s_SomeItem9s', 'SomeItem2_SomeItem9_SomeItem2_id', 'SomeItem2_SomeItem9_SomeItem9_id')
            ->withPivot('SomeItem2_SomeItem9_value', 'SomeItem2_SomeItem9_SomeItem5')
            ->withTimestamps();
    }

    public function checkSomeItem9($SomeItem9_id, $SomeItem9_value, $volume){
        return $this->SomeItem9s()
            ->wherePivot('SomeItem2_SomeItem9_SomeItem9_id', $SomeItem9_id)
            ->wherePivot('SomeItem2_SomeItem9_value', '<=', $SomeItem9_value + $volume)
            ->wherePivot('SomeItem2_SomeItem9_value', '>=', $SomeItem9_value - $volume)
            ->first()==false ? false : true;
    }

    public function SomeItem8s()
    {
        return $this->hasMany('App\SomeItem8', 'SomeItem8_rated_id')->where('SomeItem8_type', 'SomeItem2');
    }


    public function SomeItem7s()
    {
        return $this->hasMany('App\SomeItem7', 'SomeItem7_SomeItem7ed_id')
            ->with(array('SomeItem5'=>function($query){
                $query->select('SomeItem5_id', 'login', 'name_f', 'name_l');
            }))
            ->where('SomeItem7_type', 'SomeItem2');
    }

    public function approvedSomeItem7s()
    {
        return $this->hasMany('App\SomeItem7', 'SomeItem7_SomeItem7ed_id')
            ->with(array('SomeItem5'=>function($query){
                $query->select('SomeItem5_id', 'login', 'name_f', 'name_l');
            }))
            ->where('SomeItem7_approved', 1)
            ->where('SomeItem7_type', 'SomeItem2');
    }

    public function favorites()
    {
        return $this->hasMany('App\WishFav', 'wish_fav_SomeItem2_id')
            ->where('wish_fav_type', 'favorite');
    }

    public function wishes()
    {
        return $this->hasMany('App\WishFav', 'wish_fav_SomeItem2_id')
            ->where('wish_fav_type', 'wish');
    }

    public function hides()
    {
        return $this->hasMany('App\Hide', 'hide_SomeItem2_id');
    }

    public function isFavorite(SomeItem5 $SomeItem5)
    {
        if($this->favorites()->where('wish_fav_SomeItem5_id', $SomeItem5->SomeItem5_id)->first() !=null){
            return true;
        }
        return false;

    }

    public function isWish(SomeItem5 $SomeItem5)
    {
        if($this->wishes()->where('wish_fav_SomeItem5_id', $SomeItem5->SomeItem5_id)->first() !=null){
            return true;
        }
        return false;
    }

    public function isHidden(SomeItem1 $SomeItem1, SomeItem5 $SomeItem5)
    {
        if($this->hides()
                ->where('hide_SomeItem1_id', $SomeItem1->SomeItem1_id)
                ->where('hide_SomeItem5_id', $SomeItem5->SomeItem5_id)
                ->first() !=null
        ){
            return true;
        }
        return false;
    }

    public function toggleFavorite(SomeItem5 $SomeItem5)
    {
        $favorite_entry = $this->favorites()->withTrashed()->where('wish_fav_SomeItem5_id', $SomeItem5->SomeItem5_id)->first();

        if($favorite_entry == null){
            WishFav::create([
                'wish_fav_SomeItem2_id' => $this->SomeItem2_id,
                'wish_fav_SomeItem5_id' => $SomeItem5->SomeItem5_id,
                'wish_fav_type' => 'favorite'
            ]);

            $this->SomeItem2_favourite_count=$this->SomeItem2_favourite_count+1;

            $this->save();
        }
        elseif($favorite_entry->deleted_at!=null){
            $this->SomeItem2_favourite_count=$this->SomeItem2_favourite_count+1;

            $this->save();

            $favorite_entry->restore();
        }
        else{
            $this->SomeItem2_favourite_count=$this->SomeItem2_favourite_count-1;

            $this->save();

            $favorite_entry->delete();
        }
    }

    public function toggleWish(SomeItem5 $SomeItem5)
    {
        $wish_entry = $this->wishes()->withTrashed()->where('wish_fav_SomeItem5_id', $SomeItem5->SomeItem5_id)->first();

        if($wish_entry == null){
            WishFav::create([
                'wish_fav_SomeItem2_id' => $this->SomeItem2_id,
                'wish_fav_SomeItem5_id' => $SomeItem5->SomeItem5_id,
                'wish_fav_type' => 'wish'
            ]);

            $this->SomeItem2_wishlist_count=$this->SomeItem2_wishlist_count+1;

            $this->save();
        }
        elseif($wish_entry->trashed()){
            $this->SomeItem2_wishlist_count=$this->SomeItem2_wishlist_count+1;

            $this->save();

            $wish_entry->restore();
        }
        else{
            $this->SomeItem2_wishlist_count=$this->SomeItem2_wishlist_count-1;

            $this->save();

            $wish_entry->delete();
        }
    }

    public function toggleHidden(SomeItem1 $SomeItem1, SomeItem5 $SomeItem5)
    {
        $hide_entry = $this
            ->hides()
            ->withTrashed()
            ->where('hide_SomeItem1_id', $SomeItem1->SomeItem1_id)
            ->where('hide_SomeItem5_id', $SomeItem5->SomeItem5_id)
            ->first();

        if($hide_entry == null){
            Hide::create([
                'hide_SomeItem2_id' => $this->SomeItem2_id,
                'hide_SomeItem5_id' => $SomeItem5->SomeItem5_id,
                'hide_SomeItem1_id' => $SomeItem1->SomeItem1_id
            ]);
        }
        elseif($hide_entry->trashed()){
            $hide_entry->restore();
        }
        else{
            $hide_entry->delete();
        }
    }

    public function SomeItem5SomeItem8(SomeItem5 $SomeItem5)
    {
        return $this->SomeItem8s()->where('SomeItem8_SomeItem5_id', $SomeItem5->SomeItem5_id)->first();
    }

    public function rate(SomeItem5 $SomeItem5, $SomeItem8)
    {
        $SomeItem5_SomeItem8 = $this->SomeItem5SomeItem8($SomeItem5);

        if($SomeItem5_SomeItem8==null){
            SomeItem8::create([
                'SomeItem8_type' => 'SomeItem2',
                'SomeItem8_SomeItem5_id' => $SomeItem5->SomeItem5_id,
                'SomeItem8_SomeItem8' => $SomeItem8,
                'SomeItem8_rated_id' => $this->SomeItem2_id
            ]);

            $this->SomeItem2_SomeItem8_count++;
        }
        else{
            $SomeItem5_SomeItem8->SomeItem8_SomeItem8 = $SomeItem8;

            $SomeItem5_SomeItem8->save();
        }

        $this->SomeItem2_SomeItem8 = $this->SomeItem8s()->avg('SomeItem8_SomeItem8');

        $this->save();
    }

    public function SomeItem5SomeItem7(SomeItem5 $SomeItem5)
    {
        return $this->SomeItem7s()->where('SomeItem7_SomeItem5_id', $SomeItem5->SomeItem5_id)->first();
    }

    public function SomeItem7(SomeItem5 $SomeItem5, $SomeItem7_content)
    {
        $SomeItem5_SomeItem7 = $this->SomeItem5SomeItem7($SomeItem5);

        if($SomeItem5_SomeItem7==null && $SomeItem7_content!=''){
            $SomeItem5_SomeItem7 = SomeItem7::create([
                'SomeItem7_type' => 'SomeItem2',
                'SomeItem7_SomeItem5_id' => $SomeItem5->SomeItem5_id,
                'SomeItem7_content' => $SomeItem7_content,
                'SomeItem7_SomeItem7ed_id' => $this->SomeItem2_id,
                'SomeItem7_approved' => null
            ]);

            if(SomeAuthService::checkEditorPermission()){
                $SomeItem5_SomeItem7->approve();
            }

        }
        elseif($SomeItem5_SomeItem7 != null){
            $SomeItem5_SomeItem7->SomeItem7_content = $SomeItem7_content;

            $SomeItem5_SomeItem7->SomeItem7_approved = null;

            if($SomeItem7_content!=''){
                if(SomeAuthService::checkEditorPermission()){
                    $SomeItem5_SomeItem7->approve();
                }
                else{
                    $SomeItem5_SomeItem7->save();
                }
            }
            else{
                $SomeItem5_SomeItem7->delete();

                $this->SomeItem2_SomeItem7_count--;
            }
        }

        $this->save();
    }

    public function hit()
    {
        $this->SomeItem2_hits++;

        $this->save();
    }

    public function lastEditorSomeItem8()
    {
        return $this->SomeItem8s()->whereIn('SomeItem8_SomeItem5_id', SomeItem5::editorsIds())->orderBy('updated_at', 'desc')->first();
    }

    public function SomeItem4SomeItem1()
    {
        if($this->SomeItem2_is_SomeItem4 == null){
            return null;
        }

        if(strtolower(explode('/', $this->SomeItem2_path)[2]) == 'unrelated'){
            return null;
        }

        return SomeItem1::find(explode('/', $this->SomeItem2_path)[2]);
    }

    public function getRealPath($without_name = false)
    {
        if($this->SomeItem2_is_SomeItem4 == 1){
            $path = Config::get('SomeItem2systems.disks')['Project_SomeItem5s_disk']['root'];
        }
        else{
            $path = Config::get('SomeItem2systems.disks')['Project_SomeItem2s_disk']['root'];
        }

        if($without_name){
            return $path.'/'.$this->SomeItem2_path;
        }

        return $path.'/'.$this->SomeItem2_path.'/'.$this->SomeItem2_name;
    }


    public function getName(){
        if($this->SomeItem2_display_name == null){

            $before_extension = substr($this->SomeItem2_name, 0, strripos($this->SomeItem2_name, '.'));

            if(!$before_extension){
                $pre_name = $this->SomeItem2_name;
            }
            else{
                $pre_name = $before_extension;
            }

            return ucwords(str_replace('_', ' ', $pre_name), ' ');
        }
        return $this->SomeItem2_display_name;
    }

    public function getNameWithoutExtension()
    {
        if(!strripos($this->SomeItem2_name, '.')){
            return false;
        }

        return substr($this->SomeItem2_name, 0, strripos($this->SomeItem2_name, '.'));
    }

    public function createSomeItem2DisplayName()
    {
        $before_extension = substr($this->SomeItem2_name, 0, strripos($this->SomeItem2_name, '.'));

        if(!$before_extension){
            $pre_name = $this->SomeItem2_name;
        }
        else{
            $pre_name = $before_extension;
        }

        $this->SomeItem2_display_name = ucwords(str_replace('_', ' ', $pre_name), ' ');

        $this->save();

        return $this->SomeItem2_display_name;
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
        if(in_array($this->getExtension(), \Config::get('Projectconfig.allowed_extensions'))){
            return true;
        }
        return false;
    }

    public function getExtension()
    {
        if(!strripos($this->SomeItem2_name, '.')){
            return false;
        }

        return substr($this->SomeItem2_name, strripos($this->SomeItem2_name, '.')+1);
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
        if( $this->SomeItem2_thumbnail!=null && !$force && !$this->hasDefaultThumbnail()){
            return  false;
        }

        $path = Config::get('SomeItem2systems.disks')['Project_SomeItem2s_meta_disk']['root']."/".$this->SomeItem2_id;

        if(!\SomeItem2::isDirectory($path)){
            \SomeItem2::makeDirectory($path);
        }

        if($this->isPdf()){

            $command = Config::get('Projectconfig.sejda_path')
                ." pdftojpeg "
                ."-j overwrite "
                ."-f '".$this->getRealPath()."'"." "
                ." -s 1"
                ." -o "
                ."'".$path."'"
                ." -r 72";

            $result = $this->executeSejdaCommand($command);

            if($result){

                // Sejda saves with 1_ prefix, that is needed to be removed. If SomeItem2 is already exists, it wil be overwritten
                \SomeItem2::move($path."/1_".$this->getNameWithoutExtension().".jpg", $path."/".$this->getNameWithoutExtension().".jpg");

                $this->SomeItem2_thumbnail = $this->SomeItem2_id."/".$this->getNameWithoutExtension().".jpg";

                $this->save();

                return true;
            }

        }

        $default_SomeItem2s = Storage::disk('Project_SomeItem2s_meta_disk')->SomeItem2s(Config::get('Projectconfig.SomeItem2_default_SomeItem6'));

        $thumbnail_name = Config::get('Projectconfig.SomeItem2_default_SomeItem6')."/".Config::get('Projectconfig.SomeItem2_thumbnail_default');

        foreach ($default_SomeItem2s as $default_SomeItem2){
            $default_name = substr(substr($default_SomeItem2, 0, strripos($default_SomeItem2, '.')), strripos($default_SomeItem2, '/')+1);

            if ($default_name == $this->getExtension()){
                $thumbnail_name = $default_SomeItem2;
            }
        }

        $this->SomeItem2_thumbnail = $thumbnail_name;

        $this->save();

        return true;
    }

    public function extractPagesFromSomeItem2(SomeItem2 $SomeItem2, $start = null, $end = null, $all = false)
    {
        if($this->SomeItem2_is_SomeItem4 !==1){
            return false;
        }

        if($all){
            $selection = "all";
        }
        else{
            $selection = $start."-".$end;
        }

        $command = Config::get('Projectconfig.sejda_path')
            ." merge -f "
            ."'".$this->getRealPath()."'"." "
            ."'".$SomeItem2->getRealPath()."'"
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
            $this->writeSpecialHeader($SomeItem2->getName(), $header_page_number);

            $this->SomeItem2_num_pages = $this->getRealNumberOfPages();

            $this->SomeItem2_size = $this->getRealSomeItem2Size();

            $SomeItem2->SomeItem2_extraction_hits++;

            $SomeItem2->save();

            $this->save();

            return true;
        }

        return false;

    }

    public function createNewSomeItem4(SomeItem2 $SomeItem2, $start, $end, $all=false){
        if($all){
            $selection = "all:";
        }
        else{
            $selection = $start."-".$end;
        }

        $command = Config::get('Projectconfig.sejda_path')
            ." merge -f "
            ."'".$SomeItem2->getRealPath()."'"
            ." -s ".$selection.":"
            ." -o "
            ."'".$this->getRealPath()."'"
            ." --overwrite";

        $header_page_number = 1;

        $merge_result = $this->executeSejdaCommand($command);

        if($merge_result){
            $this->writeSpecialHeader($SomeItem2->getName(), $header_page_number);

            $this->SomeItem2_num_pages = $this->getRealNumberOfPages();

            $this->SomeItem2_size = $this->getRealSomeItem2Size();

            $SomeItem2->SomeItem2_extraction_hits++;

            $SomeItem2->save();

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


        if($this->SomeItem2_is_SomeItem4 !==1){
            return false;
        }

        $command = Config::get('Projectconfig.sejda_path')
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

        $command = Config::get('Projectconfig.pdfinfo_path')
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

    public function getRealSomeItem2Size()
    {
        return SomeItem2size($this->getRealPath());
    }


    public function lock(SomeItem5 $SomeItem5){
        $this->SomeItem2_SomeItem9s_locked_by = $SomeItem5->SomeItem5_id;

        $this->SomeItem2_SomeItem9s_lock = date("Y-m-d H:i:s");

        $this->save();
    }

    public function unlock(){
        $this->SomeItem2_SomeItem9s_locked_by = null;

        $this->SomeItem2_SomeItem9s_lock = null;

        $this->save();
    }

    public static function updateAllThumbnails($force = false)
    {
        SomeItem2::chunk(100, function($SomeItem2s) use ($force) {
            foreach ($SomeItem2s as $SomeItem2){
                $SomeItem2->generateThumbnail(false); // Aways $force == false
            }
        });

        return true;
    }

    public function thumbnail()
    {
        if(!$this->SomeItem2_thumbnail){
            return null;
        }

        $SomeItem2s_meta_disk = Storage::disk(\Config::get('Projectconfig.Project_SomeItem2s_meta_disk'));
        return $SomeItem2s_meta_disk->read($this->SomeItem2_thumbnail);
    }

    public function deleteSomeItem4(){
        if($this->SomeItem2_is_SomeItem4 != 1){
            return false;
        }

        $SomeItem2s_meta_disk = Storage::disk(\Config::get('Projectconfig.Project_SomeItem2s_meta_disk'));

        $SomeItem2s_meta_disk->deleteDir($this->SomeItem2_id);

        $SomeItem2s_meta_disk = Storage::disk(\Config::get('Projectconfig.Project_SomeItem5s_disk'));

        $SomeItem2s_meta_disk->delete($this->SomeItem2_path."/".$this->SomeItem2_name);

        $this->delete();

        return true;
    }

    public function deleteSomeItem2()
    {
        $this->SomeItem9s()->detach();
        $this->SomeItem7s()->delete();
        $this->wishes()->forceDelete();
        $this->favorites()->forceDelete();
        $this->SomeItem8s()->delete();
        $this->hides()->forceDelete();
        $this->delete();

        $SomeItem2s_meta_disk = Storage::disk(\Config::get('Projectconfig.Project_SomeItem2s_meta_disk'));

        $SomeItem2s_meta_disk->deleteDir($this->SomeItem2_id);
    }

    public function relatedSomeItem1()
    {
        if($this->SomeItem2_is_SomeItem4 != 1){
            return null;
        }

        $parts = explode('/', $this->SomeItem2_path);

        $SomeItem1_id = $parts[count($parts)-1];

        if(is_numeric($SomeItem1_id)){
            return SomeItem1::find($SomeItem1_id)->SomeItem1_name;
        }

        return $SomeItem1_id;
    }

}
