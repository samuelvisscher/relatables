<?php

namespace Visscher\Relatables;

class RelatableListener
{
    /**
     * A list with relations which should be reloaded, due to changes in their relatables
     *
     * @var array
     */
    private $reloadableRelations = [];

    /**
     * Iterate over each object
     */
    public function handle($event, $objects)
    {
        foreach ($objects as $object) {
            $this->createRelatableObjects($object);
        }
    }

    /**
     * Check for each listened object if there any relatable objects which should still be created or updated
     *
     * @param $object
     */
    public function createRelatableObjects($object)
    {
        if (property_exists($object, 'relatableObjects') && count($object->relatableObjects)) {
            foreach ($object->relatableObjects as $relatable) {
                $object->createOrUpdateRelatable($relatable->data, $relatable->relation);
                $this->reloadableRelations[] = $relatable->method;
            }

            foreach (array_unique($this->reloadableRelations) as $relation) {
                $object->unsetRelation($relation);
                $object->$relation;
            }

            unset($object->relatableObjects);
        }
    }
}
