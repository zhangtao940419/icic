<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'fictitious_coin_type';

    protected $primaryKey = 'coin_id';

    protected $fillable = ['coin_name', 'is_usable'];
}
