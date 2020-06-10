<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Traits\RedisTool;


class InsideInfo implements ShouldQueue
{
    use RedisTool;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $insideTrade;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($insideTrade)
    {
        $this->insideTrade = $insideTrade;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $key = $this->insideTrade['base_coin_name'] . '_' . $this->insideTrade['exchange_coin_name'] . '_info';
        $info = [1, 5, 15, 30, 60];
            if ($this->redisExists($key)) {
                 foreach ($info as $v) {
                    if (!isset($this->redisHgetAll($key)['is_trade_' . $v])) {
                        $this->redisHset($key, 'is_trade_' . $v, 0);
                    }
                    if ($this->redisHgetAll($key)['is_trade_' . $v] == 0) {
                        $this->redisHset($key, 'begin_' . $v, $this->insideTrade['current_price']);
                        $this->redisHset($key, 'is_trade_' . $v, 1);
                        $this->redisHset($key, 'vol_' . $v, $this->insideTrade['vol']);
                        $this->redisHset($key, 'end_' . $v, $this->insideTrade['current_price']);
                        $this->redisHset($key, 'max_' . $v, $this->insideTrade['current_price']);
                        $this->redisHset($key, 'min_' . $v, $this->insideTrade['current_price']);
                    } else {
                        $this->hxIncrement($key, 'is_trade_' . $v, 1);
                        $this->redisHmset($key, ['end_' . $v => $this->insideTrade['current_price']]);
                        $this->hxIncrement($key, 'vol_' . $v, $this->insideTrade['vol']);
                        $max = $this->redisHget($key, 'max_' . $v);
                        $min = $this->redisHget($key, 'min_' . $v);
                        $this->insideTrade['current_price'] > $max ? $this->redisHset($key, 'max_' . $v, $this->insideTrade['current_price']) : 0;
                        $this->insideTrade['current_price'] < $min ? $this->redisHset($key, 'min_' . $v, $this->insideTrade['current_price']) : 0;
                    }

                }
            } else {
                foreach ($info as $newValue){
                    $this->redisHmset($key, ['min_' . $newValue => $this->insideTrade['current_price'],
                                             'max_' . $newValue => $this->insideTrade['current_price'],
                                             'vol_' . $newValue => $this->insideTrade['vol'],
                                             'begin_' . $newValue => $this->insideTrade['current_price'],
                                             'end_' . $newValue => $this->insideTrade['current_price'],
                                            ]);

                }

            }
        }

}
