<?php

namespace bachphuc\LaravelSettings\Facades;

use Illuminate\Support\Facades\Facade;

class AppSettingFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'app_setting'; }

    public static function routes($params = []){
        $router = static::$app->make('router');

        $namespace = '\bachphuc\LaravelSettings\Http\Controllers\\';
        $controller = 'SettingController';
        if(isset($params['namespace'])){
            $namespace = $params['namespace'];
        }

        if(isset($params['controller'])){
            $controller = $params['controller'];
        }

        $router->resource('/settings', $namespace. $controller);
        $router->post('/update-settings', $namespace. $controller. '@updateSettings');
        $router->get('/settings/export/json', $namespace . $controller . '@export')->name('settings.export');
        $router->any('/settings/import/json', $namespace. $controller . '@import')->name('settings.import');
        $router->get('/settings//action/clone', $namespace . $controller . '@clone')->name('settings.clone');
    }
}