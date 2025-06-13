<?php

namespace App\Listeners;

use App\Events\CreditTenorEvent;
use App\Models\CreditTenor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreditTenorListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CreditTenorEvent $event): void
    {
        $credit = $event->credit;
        $tenor = (int) $credit->tenor;
        $money = (int) $credit->nominal / $tenor;

        for ($i = 0; $i < $tenor; $i++) {
            CreditTenor::create([
                'credit_id' => $credit->id,
                'nominal' => $money,
            ]);
        }
    }
}
