<?php

namespace TakeawayTown\LaravelUuid\Traits;

use TakeawayTown\LaravelUuid\Classes\Uuid;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait Uuid
{
    /**
     * Boot function from laravel.
     */
    protected static function boot()
    {
        parent::boot();
    }
}
