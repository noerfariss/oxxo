<?php

namespace App\Class;

use App\Enums\DepositEnum;
use App\Models\Deposit;
use App\Models\DepositCutOff;
use App\Models\Member;

class DepositClass
{
    public static function saldo(Member $member)
    {
        $cutoff = DepositCutOff::query()
            ->where('member_id', $member->id)
            ->orderBy('id', 'desc')
            ->first();

        // jika belum ada cutoff
        if (!$cutoff) {
            $totalCutOff =  0;

            $in = Deposit::query()
                ->where('member_id', $member->id)
                ->where('type', DepositEnum::IN->value)
                ->sum('amount');

            $out = Deposit::query()
                ->where('member_id', $member->id)
                ->where('type', DepositEnum::OUT->value)
                ->sum('amount');
        } else {
            $totalCutOff = $cutoff->total;

            $in = Deposit::query()
                ->where('member_id', $member->id)
                ->where('type', DepositEnum::IN->value)
                ->where('dates', '>', $cutoff->dates)
                ->sum('amount');

            $out = Deposit::query()
                ->where('member_id', $member->id)
                ->where('type', DepositEnum::OUT->value)
                ->where('dates', '>', $cutoff->dates)
                ->sum('amount');
        }

        $total = ($totalCutOff + $in) - $out;

        return number_format($total, 0);
    }
}
