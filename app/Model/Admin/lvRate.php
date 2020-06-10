<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class lvRate extends Model
{
    protected $table = 'lv_rate';

    protected $fillable = ['lv1', 'lv2', 'lv3'];
}
