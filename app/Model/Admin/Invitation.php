<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $table = 'invitation_prize';

    protected $fillable = ['coin_id', 'user_id', 'coin_num'];
}
