<?php
namespace TakeawayTown\LaravelUuid\Facades;

use Illuminate\Support\Facades\Facade;

class Uuid extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'uuid';
    }
}
