<?php

namespace Modules\Setting\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
  public $timestamps = false;

  public $fillable = ['value'];

}