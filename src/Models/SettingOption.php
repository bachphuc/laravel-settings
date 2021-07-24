<?php

namespace bachphuc\LaravelSettings\Models;

use Illuminate\Database\Eloquent\Model;

class SettingOption extends Model
{
    protected $table = 'dsoft_setting_options';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'setting_key',
        'option_title',
        'option_value'
    ];

    public static function createOption($params = array()){
        $option = SettingOption::where('setting_key', $params['setting_key'])
        ->where('option_value' , $params['option_value'])->first();
        if($option) return $option;

        $option = SettingOption::create($params);

        return $option;
    }
}
