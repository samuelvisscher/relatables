<?php

namespace Visscher\Relatables;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRelatables
{
    /**
     * Any relatable object, which will after save be related
     *
     * @var array
     */
    public $relatableObjects = [];

    /**
     * Expanding Eloquent's standard set attribute functionality, to handle any attributes as relatable if they
     * indeed have been flagged as such
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        // Check if there is a relatable available
        if (!in_array($key, $this->getRelatables()) || !method_exists($this, camel_case($key))) {
            return parent::setAttribute($key, $value);
        }

        $method = camel_case($key);
        $this->setRelatableObjects($value, $this->$method(), $method);
    }

    /**
     * Relatables getter
     *
     * @return array
     */
    public function getRelatables()
    {
        return isset($this->relatables) ? $this->relatables : [];
    }

    /**
     * Create a relatable object on the model
     *
     * @param $values
     * @param $relation
     * @param $method
     */
    public function setRelatableObjects($values, $relation, $method)
    {
        foreach ($values as $value) {
            $this->relatableObjects[] = new Relatable($value, $relation, $method);
        }
    }

    /**
     * Create or update a model based on a relatable, supports both HasMany relations and MorphMany relations
     *
     * @param $data
     * @param $relation
     */
    public function createOrUpdateRelatable($data, $relation)
    {
        switch(get_class($relation)) {
            case "Illuminate\\Database\\Eloquent\\Relations\\HasMany":
                $this->createOrUpdateHasMany($data, $relation);
                break;
            case "Illuminate\\Database\\Eloquent\\Relations\\MorphMany":
                $this->createOrUpdateMorphMany($data, $relation);
                break;
        }
    }

    /**
     * Persist an object from a HasMany relationship
     *
     * @param $data
     * @param $relation
     */
    private function createOrUpdateHasMany($data, HasMany $relation)
    {
        $data = array_add($data, $relation->getForeignKeyName(), $this->id);

        $relation->getRelated()->createOrUpdate($data);
    }

    /**
     * Persist an object from a MorphMany relationship
     *
     * @param $data
     * @param $relation
     */
    private function createOrUpdateMorphMany($data, MorphMany $relation)
    {
        $morphable = [
            $relation->getForeignKeyName() => $this->id,
            $relation->getMorphType() => $relation->getMorphClass()
        ];

        $relation->getRelated()->createOrUpdate(array_merge($data, $morphable));
    }

    /**
     * Persist an object based on its attributes, updating if an ID is present, creating if no ID is present
     *
     * @param $objectData
     * @return mixed
     */
    public static function createOrUpdate($objectData)
    {
        if (isset($objectData['id'])) {
            $object = self::findOrFail($objectData['id']);
            $object->update($objectData);
        } else {
            $object = self::create($objectData);
        }

        return $object;
    }

    /**
     * Get the fillable attributes for the model and merge it with any relatables
     *
     * @return array
     */
    public function getFillable()
    {
        return array_merge($this->getRelatables(), $this->fillable);
    }

    /**
     * Allow a relation to be unset
     *
     * @param $relationName
     */
    public function unsetRelation($relationName)
    {
        unset($this->relations[$relationName]);
    }
}