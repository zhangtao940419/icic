<?php

namespace App\Observers;

use App\Model\OutsideWalletDetail;

class OutsideWalletDetailObserver
{
    /**
     * Handle the outside wallet detail "created" event.
     *
     * @param  \App\Model\OutsideWalletDetail  $outsideWalletDetail
     * @return void
     */
    public function created(OutsideWalletDetail $outsideWalletDetail)
    {
        //
    }

    /**
     * Handle the outside wallet detail "updated" event.
     *
     * @param  \App\Model\OutsideWalletDetail  $outsideWalletDetail
     * @return void
     */
    public function updated(OutsideWalletDetail $outsideWalletDetail)
    {
        // TODO 用户场外钱包更新时  当钱包货币可用余额低于设置值时关闭对应货币广告委托
        
    }

    /**
     * Handle the outside wallet detail "deleted" event.
     *
     * @param  \App\Model\OutsideWalletDetail  $outsideWalletDetail
     * @return void
     */
    public function deleted(OutsideWalletDetail $outsideWalletDetail)
    {
        //
    }

    /**
     * Handle the outside wallet detail "restored" event.
     *
     * @param  \App\Model\OutsideWalletDetail  $outsideWalletDetail
     * @return void
     */
    public function restored(OutsideWalletDetail $outsideWalletDetail)
    {
        //
    }

    /**
     * Handle the outside wallet detail "force deleted" event.
     *
     * @param  \App\Model\OutsideWalletDetail  $outsideWalletDetail
     * @return void
     */
    public function forceDeleted(OutsideWalletDetail $outsideWalletDetail)
    {
        //
    }
}
