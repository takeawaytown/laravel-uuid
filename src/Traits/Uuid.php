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

        static::creating(function ($model) {
            if (!$model->{config('uuid.uuid_column')}) {
                $model->{config('uuid.uuid_column')} = strtoupper(Uuid::uuid4()->toString());
            }
        });

        static::saving(function ($model) {
            $original_uuid = $model->getOriginal(config('uuid.uuid_column'));
            if ($original_uuid !== $model->{config('uuid.uuid_column')}) {
                $model->{config('uuid.uuid_column')} = $original_uuid;
            }
        });
    }
    /**
     * Scope  by uuid
     * @param  string  uuid of the model.
     *
    */
    public function scopeUuid($query, $uuid, $first = true)
    {
        $match = preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/', $uuid);
        if (!is_string($uuid) || $match !== 1)
        {
            throw (new ModelNotFoundException)->setModel(get_class($this));
        }

        $results = $query->where(config('uuid.uuid_column'), $uuid);

        return $first ? $results->firstOrFail() : $results;
    }
}
