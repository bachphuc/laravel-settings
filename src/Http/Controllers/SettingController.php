<?php

namespace bachphuc\LaravelSettings\Http\Controllers;

use bachphuc\LaravelSettings\Models\Setting;
use bachphuc\LaravelSettings\Models\SettingGroup;
use bachphuc\LaravelSettings\Models\SettingOption;
use Illuminate\Http\Request;
use Session;
use File;
use Response;
use Cache;

class SettingController extends Controller
{
    protected $activeMenu = 'settings';
    protected $layout = 'elements::layouts.admin';
    protected $prefixName = '';
    protected $colorTheme = 'white';

    public function getMenus(){
        return [];

    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Setting::where('is_active', 1)
            ->get();

        $groups = [
            'general_settings' => [
                'title' => 'General Settings',
                'group_key' => 'general_settings',
                'color' => 'purple',
                'settings' => [],
            ],
        ];
        foreach ($items as $item) {
            if (empty($item->group_key)) {
                $groups['general_settings']['settings'][] = $item;
            } elseif (isset($groups[$item->group_key])) {
                $groups[$item->group_key]['settings'][] = $item;
            } else {
                if($item->getGroup()){
                    $groups[$item->group_key] = [
                        'title' => $item->getGroup()->title,
                        'group_key' => $item->group_key,
                        'settings' => [$item],
                        'color' => $item->getGroup()->color,
                    ];
                }
            }
        }

        return view('settings::index', [
            'activeMenu' => $this->activeMenu,
            'groups' => $groups, 
            'empty_setting' => $items->count() ? false : true,
            'layout' => $this->getLayout(),
            'prefixName' => $this->prefixName,
            'menus' => $this->getMenus(),
            'colorTheme' => $this->colorTheme
        ]);
    }

    public function getLayout(){
        return $this->layout;
    }

    public function storeFile($file){
        // get original extension
        $extension = $file->getClientOriginalExtension();
        if(empty($extension)){
            $extension = 'jpg';
        }

        $path = \Storage::putFileAs(public_path('public/upload'), $file , str_random(8) . '.' .  $extension);

        return $path;
    }

    public function updateSettings(Request $request)
    {
        $data = $request->all();
        
        foreach ($data as $key => $value) {
            $key = str_replace('-', '.', $key);
            if($value instanceof \Illuminate\Http\UploadedFile){
                $path = $this->storeFile($request->{$key});
                if(!empty($path)){
                    $item = Setting::where('setting_key', $key)
                    ->first();
                    if ($item) {
                        $item->setting_value = $path;
                        $item->save();
                    }
                }
            }
            else{
                $item = Setting::where('setting_key', $key)
                ->first();
                if ($item) {
                    $item->setting_value = $value;
                    $item->save();
                }
            }
        }
        Session::flash('message', 'Update configure successfully.');
        return redirect()->to("admin/settings");
    }

    public function create(Request $request)
    {
        $groups = SettingGroup::all();
        $colors = ['purple', 'red', 'green', 'orange', 'blue'];
        return view('settings::create', [
            'activeMenu' => $this->activeMenu,
            'groups' => $groups,
            'colors' => $colors,
            'layout' => $this->getLayout(),
            'prefixName' => $this->prefixName,
            'menus' => $this->getMenus(),
            'colorTheme' => $this->colorTheme
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'setting_value' => 'required',
            'setting_type',
        ];
        $this->validate($request, $rules);

        $settingKey = $request->input('setting_key');
        if(empty($settingKey)){
            $settingKey = str_slug($request->input('title'), '_');
        }
        // check setting if exists
        $checkSetting = Setting::where('setting_key', $settingKey)
        ->first();
        if($checkSetting){
            $request->flash();
            $this->validator->errors()->add('setting_key', 'Setting is exists');
            return $this->throwValidationException($request, $this->validator);
        }
        $params = [
            'title' => $request->input('title'),
            'setting_value' => $request->input('setting_value'),
            'setting_key' => $settingKey,
            'setting_type' => $request->input('setting_type'),
        ];

        if ($request->input('group_key') == 'new_group') {
            // create new group
            if (!$request->input('group_name')) {
                $request->flash();
                $this->validator->errors()->add('group_name', 'Group name is requried');
                return $this->throwValidationException($request, $this->validator);
            }
            $groupKey = str_slug($request->input('group_name'), '_');

            $checkGroup = SettingGroup::where('group_key', $groupKey)
            ->first();
            if(!$checkGroup){
                $group = SettingGroup::create([
                    'title' => $request->input('group_name'),
                    'group_key' => $groupKey,
                    'color' => $request->input('group_color') ? $request->input('group_color') : 'purple',
                ]);
                $params['group_key'] = $group->group_key;
            }
            else{
                $params['group_key'] = $groupKey;
            }
            
        }
        else if($request->input('group_key')){
            $params['group_key'] = $request->input('group_key');
        }

        if ($request->input('setting_type') == 'select') {
            if (!empty($request->input('setting_options'))) {
                // create setting options
                $settingOptions = explode(',', $request->input('setting_options'));
                foreach ($settingOptions as $op) {
                    $optionTitle = trim($op);
                    if (!empty($optionTitle)) {
                        $option = SettingOption::create([
                            'setting_key' => $params['setting_key'],
                            'option_title' => $optionTitle,
                            'option_value' => str_slug($optionTitle, '_'),
                        ]);
                    }
                }
            }
        }

        $setting = Setting::create($params);
        Session::flash('message', 'Create setting successfully.');
        return redirect()->to("admin/settings");
    }

    public function export(Request $request){
        $items = Setting::with(['options'])
            ->where('is_active', 1)
            ->get();
            
        $groups = [
            'general_settings' => [
                'title' => 'General Settings',
                'group_key' => 'general_settings',
                'color' => 'purple',
                'settings' => [],
            ],
        ];
        foreach ($items as $item) {
            if (empty($item->group_key)) {
                $groups['general_settings']['settings'][] = $item;
            } elseif (isset($groups[$item->group_key])) {
                $groups[$item->group_key]['settings'][] = $item;
            } else {
                $groups[$item->group_key] = [
                    'title' => $item->getGroup()->title,
                    'group_key' => $item->group_key,
                    'settings' => [$item],
                    'color' => $item->getGroup()->color,
                ];
            }
        }
        $exportData = [
            'time' => date("l Y/m/d h:i:sa"),
            'data' => $groups
        ];
        $data = json_encode($exportData);
        $fileName = 'setting_' . time() . '.json';
        $path = public_path('/tmp/' . $fileName);
        File::put($path, $data);

        return Response::download($path);
    }

    public function import(Request $request){
        if($request->isMethod('POST')){
            $this->validate($request, [
                'file' => 'required'
            ]);

            $path = $request->file->store('tmp');
            $content = File::get(public_path($path));
            $data = json_decode($content, true);
            if($data && isset($data['data'])){
                $groups = $data['data'];
                foreach($groups as $groupkey => $group){
                    // create group here
                    $newGroup = SettingGroup::createGroup([
                        'title' => $group['title'],
                        'group_key' => $group['group_key'],
                        'color' => $group['color']
                    ]);

                    if(isset($group['settings']) && !empty($group['settings'])){
                        foreach($group['settings'] as $setting){
                            $newSetting = Setting::createSetting([
                                'title' => $setting['title'],
                                'setting_key' => $setting['setting_key'],
                                'setting_value' => $setting['setting_value'],
                                'group_key' => $setting['group_key'],
                                'setting_type' => $setting['setting_type'],
                                'is_active' => $setting['is_active']
                            ]);
                            // create setting here
                            if($setting['setting_type'] == 'select' && isset($setting['options']) && !empty($setting['options'])){
                                foreach($setting['options'] as $option){
                                    // create option here
                                    $newOption = SettingOption::createOption([
                                        'setting_key' => $option['setting_key'],
                                        'option_title' => $option['option_title'],
                                        'option_value' => $option['option_value']
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            Session::flash('message', 'Import setting successfully.');
            return redirect()->to("admin/settings");
        }

        return view('admin.settings.import');
    }

    public function clone(Request $request){
        $defaultSite = Mobi::getDefaultSite();

        if(!$defaultSite){
            Session::flash('error', 'No default site');
            return redirect()->to("admin/settings");
        }

        $settings = Setting::all();

        foreach($settings as $k => $setting){
            Setting::clone($setting);
        }

        Session::flash('message', 'Clone setting successfully.');
        return redirect()->to("admin/settings"); 

    }

    public function flushCache(){
        Cache::flush();
        Session::flash('message', 'Clear cache successful.');
        return redirect()->to("admin/settings"); 
    }
}
