<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $table = 'invitation_prize';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function coin()
    {
        return $this->belongsTo(CoinType::class, 'coin_id', 'coin_id');
    }

}
