<?php

if (!function_exists('setting')) {
    function setting($key, $default = null){
        return AppSetting::getSetting($key, $default);
    }
}