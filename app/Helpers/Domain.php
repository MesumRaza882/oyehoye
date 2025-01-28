<?php

namespace App\Helpers;

use Illuminate\Support\Str;

use File;
use Carbon\Carbon;
use App\Models\Admin;

/**
 * Config file for websites color scheme + other settings
 */
class Domain
{
  public static function admin($property)
  {
    $website = request()->server->get('SERVER_NAME');

    \Cache::forget('admin_'.$website);
    $admin = \Cache::rememberForever('admin_'.$website, function() use($website) {
      return Admin::where('website', $website)->latest()->first();
    });

    if(!$admin)
      abort('404');

    return $admin->$property;

	}
}