<?php

namespace bachphuc\LaravelSettings\Models;

use Illuminate\Database\Eloquent\Model;

use Cache;

class SettingGroup extends Model
{
    protected $table = 'dsoft_setting_groups';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'color',
        'group_key',
        'site_id'
    ];

    protected $cacheSettings;
    protected static $cacheAllGroups = null;

    public static function createGroup($params = []){
        $group = SettingGroup::where('group_key', $params['group_key'])
        ->first();
        if($group){
            return $group;
        }

        if(!isset($params['title'])){
            $params['title'] = ucfirst(str_replace('_', ' ', $params['group_key']));
        }

        $group = SettingGroup::create($params);
        return $group;
    }

    public static function findOrCreate($params){
        return self::createGroup($params);
    }

    public static function clone($group){
        $data = $group->toArray();
        return self::createGroup($data);
    }

    public function settings(){
        return $this->hasMany('\bachphuc\LaravelSettings\Models\Setting', 'group_key', 'group_key');
    }

    public function getSettings(){
        if(!empty($this->cacheSettings)) return $this->cacheSettings;

        $cacheKey = 'group_' . $this->group_key;
        if(Cache::has($cacheKey)){
            $this->cacheSettings = Cache::get($cacheKey);
            return $this->cacheSettings;
        }

        $this->cacheSettings = Setting::where('group_key', $this->group_key)
        ->get();

        Cache::put($cacheKey, $this->cacheSettings, 1440);

        return $this->cacheSettings;
    }

    public static function getGroup($key){
        $cacheKey = 'groups';
        if(empty(self::$cacheAllGroups)){
            if(Cache::has($cacheKey)){
                self::$cacheAllGroups = Cache::get($cacheKey);
            }
            else{
                $groups = SettingGroup::all();

                self::$cacheAllGroups = [];
                foreach($groups as $group){
                    self::$cacheAllGroups[$group->group_key] = $group;
                }

                Cache::put($cacheKey, self::$cacheAllGroups, 1440);
            }
        }

        if(empty(self::$cacheAllGroups)) return null;

        if(isset(self::$cacheAllGroups[$key])){
            return self::$cacheAllGroups[$key];
        }

        return null;
    }

    public static function flushCache(){
        $cacheKey = 'groups';
        Cache::forget($cacheKey);
    }
}
