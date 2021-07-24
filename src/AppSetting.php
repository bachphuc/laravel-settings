<?php

namespace bachphuc\LaravelSettings;

use bachphuc\LaravelSettings\Models\Setting;

class AppSetting
{
    public function getSetting($key, $defaultValue = ''){
        return Setting::getSetting($key, $defaultValue);
    }

    public function saveSetting($key, $value){
        return Setting::saveSetting($key, $value);
    }
}