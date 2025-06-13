<?php

namespace App\Trait;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasScope
{
    protected static function bootHasScope()
    {
        static::addGlobalScope('user_id', function (Builder $builder) {
            $builder->where('user_id', Auth::id());
        });
    }
}
