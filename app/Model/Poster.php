<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Poster extends Model
{
    protected $table = 'poster';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function getImgurlAttribute($value)
    {
        return blank($value) ? '' : env('QI_NIU_URL') . $value;
    }

}
