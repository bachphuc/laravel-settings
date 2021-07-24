<?php

namespace bachphuc\LaravelSettings\Models;

use Illuminate\Database\Eloquent\Model;
use App\Mobi;
use bachphuc\LaravelSettings\Models\SettingGroup;
use Cache;

class Setting extends Model
{
    protected $table = 'dsoft_settings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'setting_key',
        'setting_value',
        'group_key',
        'setting_type',
        'site_id',
        'is_active',
    ];

    protected $group;
    protected static $cacheSettings = [];
    protected static $allSettings = [];

    public static function getAllSettings(){
        if(self::$allSettings) return self::$allSettings;
        $cacheKey = 'settings';

        // try to get from cache
        if(Cache::has($cacheKey)){
            $tmps = Cache::get($cacheKey);
            if(!empty($tmps)){
                self::$allSettings = $tmps;
                return self::$allSettings;
            }
        }

        // query direct from database
        $settings = Setting::all();

        $cacheData = [];
        foreach($settings as $key => $setting){
            $settingKey = !empty($setting->module) ? $setting->module . '.' . $setting->setting_key :  $setting->setting_key;
            $cacheData[$settingKey] = $setting;
        }

        self::$allSettings = $cacheData;

        // save this to cache
        Cache::put($cacheKey, self::$allSettings, 30);
        return self::$allSettings;
    }

    public static function flushCache(){
        $cacheKey = 'settings';
        Cache::forget($cacheKey);
    }

    public static function getSettingFromCache($settingKey){
        if(empty($settingKey)) return null;
        $allSettings = self::getAllSettings();
        if(empty($allSettings)) return null;
        if(isset($allSettings[$settingKey])) return $allSettings[$settingKey];

        return null;
    }

    public static function getSetting($key, $defaultValue = ''){
        if(isset(self::$cacheSettings[$key])){
            return self::$cacheSettings[$key];
        }     

        $item = self::getSettingFromCache($key);
        
        if(!$item) {
            self::$cacheSettings[$key] = $defaultValue;
            return $defaultValue;
        }

        self::$cacheSettings[$key] = $item->setting_value;
        return $item->setting_value;
    }

    public function getFormSettingKey(){
        return str_replace('.', '-', $this->setting_key);
    }

    public function settingGroup()
    {
        return $this->belongsTo('\bachphuc\LaravelSettings\Models\SettingGroup', 'group_key', 'group_key');
    }

    public function options(){
        return $this->hasMany('\bachphuc\LaravelSettings\Models\SettingOption', 'setting_key', 'setting_key');
    }

    public static function createSetting($params = []){
        $settingKey = $params['setting_key'];
        $groupKey = null;
        if(strpos($settingKey, '.') !== false){
            $tmp = explode('.', $settingKey);
            $groupKey = $tmp[0];
            $settingKey = $tmp[1];
        }
        $query = Setting::where('setting_key', $settingKey);
        if(!empty($groupKey)){
            $query->where('group_key', $groupKey);
        }
        $setting = $query->first();

        if($setting) return $setting;
        if(!empty($groupKey)){
            // get group or create a new group
            $group = SettingGroup::findOrCreate([
                'group_key' => $groupKey
            ]);

            $params['group_key'] = $groupKey;
        }
        if(!isset($params['title'])){
            $params['title'] = ucfirst(str_replace('_', ' ', $settingKey));
        }
        if(!isset($params['setting_type'])){
            $params['setting_type'] = 'text';
        }
        $setting = Setting::create($params);
        self::flushCache();
        return $setting;
    }

    public static function findOrCreate($params){
        return self::createSetting($params);
    }

    public static function saveSetting($key, $value){
        $setting = self::findOrCreate([
            'setting_key' => $key,
            'setting_value' => $value
        ]);

        if($setting->setting_value != $value){
            $setting->setting_value = $value;
            $setting->save();
            self::flushCache();
        }
        
        return $setting;
    }

    public static function clone($setting){
        $data = $setting->toArray();
        $group = $setting->getGroup();
        if($group){
            SettingGroup::clone($group);
        }
        
        return self::createSetting($data);
    }

    public static function getSettingArray($key, $delimiter = ','){
        $str = self::getSetting($key);
        if(empty($str)) return [];
        $ars = explode($delimiter, $str);
        if(empty($ars)) return [];
        $result = [];
        foreach($ars as $ar){
            $s = trim($ar);
            if(!empty($s)){
                $result[] = $s;
            }
        }
        return $result;
    }

    public static function getSettingNumberArray($key, $delimiter = ','){
        $str = self::getSetting($key);
        if(empty($str)) return [];
        $ars = explode($delimiter, $str);
        if(empty($ars)) return [];
        $result = [];
        foreach($ars as $ar){
            $s = trim($ar);
            if(!empty($s)){
                $result[] = (int) $s;
            }
        }
        return $result;
    }

    public function getGroup(){
        if(!empty($this->group)) return $this->group;

        $this->group = SettingGroup::where('group_key', $this->group_key)
        ->first();

        return $this->group;
    }
}
