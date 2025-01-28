<?php

namespace Modules\Setting\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Setting\Http\Models\Setting;
use Illuminate\Support\Facades\Route;
use Cache;
use App\Models\Admin;

class SettingController extends Controller
{
  public function index()
  {
    $route = Route::current()->getName();

    $title = str_replace(['.', 'admin', 'index'], ['', '', ''], $route);

    $settings = Setting::where('route', $route)
    ->where('is_hidden', 0)
    ->where('admin_id', auth()->user()->id)
    ->orderBy('sort', 'asc')->get();

    return view('setting::admin.index', compact('settings', 'title'));
  }
  
  public function save(Request $request)
  {
    // return $request->all();
    foreach ($request->except('_token') as $index => $value) {
      $setting = Setting::whereAttribute($index)->where('admin_id', auth()->user()->id)->first();
      if($setting){
        if($setting->type == 'file'){
          $this->delete_previous_image($data->image);
          $setting->value = $this->upload_image($meta_value, "settings");
        }else{
          $setting->value = $value;
        }
        $setting->save();
  
        Cache::forget('zahidaz_setting_'.$index);
      }
    }

    $admins = Admin::get();
    foreach($admins as $admin){
      Cache::forget('zahidaz_settings_'.$admin->id);
    }

    return redirect()->back()->with("message", "Settings has been updated");
  }

  public static function getSetting($attribute)
  {
    $setting = Cache::rememberForever('zahidaz_setting_'.$attribute, function() use($attribute) {
      return Setting::where('attribute', $attribute)->first();
    });
  }

  public static function getSettings()
  {
    $setting = Cache::rememberForever('zahidaz_settings', function() use($attribute) {
      return Setting::select('attribute', 'value')->get();
    });
  }
  

  // delete previous image
  public function delete_previous_image($pre_file)
  {
      $path = str_replace(url('/').'/' , "", $pre_file);
      if(File::exists($path))
      {
          File::delete($path);
      }
  }

  public function upload_image($file, $path = '',  $name = null)
  {
      $ext = $file->getClientOriginalExtension();
      $filename = time().'.'.$ext;
      $file->move($path,$filename);
      $upload_path = $path.'/'.$filename;
      return url($upload_path);
  }

}