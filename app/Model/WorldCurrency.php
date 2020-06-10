<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WorldCurrency extends Model
{
    protected $table = 'world_currency';

    protected $primaryKey = 'currency_id';

    protected $fillable = ['currency_code', 'currency_cn_full_name', 'currency_en_name', 'is_usable'];
}
