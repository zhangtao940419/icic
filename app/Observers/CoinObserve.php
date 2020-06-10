<?php

namespace App\Observers;

use App\Handlers\Helpers;
use App\Model\CoinType;

class CoinObserve
{
    public function saved(CoinType $coinType)
    {
//        dd($coinType);
    }
}
