<?php

namespace ProAI\Datamapper\Eloquent;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use ProAI\Datamapper\Support\Collection as DatamapperCollection;
use ProAI\Datamapper\Eloquent\Model;

class Collection extends EloquentCollection
{
    /**
     * Convert models to plain old php objects.
     *
     * @return \ProAI\Datamapper\Support\Collection
     */
    public function toEntity()
    {
        $entities = new DatamapperCollection;

        foreach ($this->items as $name => $item) {
            $entities->put($name, $item->toEntity());
        }

        return $entities;
    }

    /**
     * Convert models to eloquent models.
     *
     * @param \ProAI\Datamapper\Support\Collection $object
     * @return \ProAI\Datamapper\Eloquent\Collection
     */
    public static function newFromEntity($entities)
    {
        $eloquentModels = new static;

        foreach ($entities as $name => $item) {
            $eloquentModels->put($name, Model::newFromEntity($item));
        }

        return $eloquentModels;
    }
}