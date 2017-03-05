<?php

namespace Visscher\Relatables;

class Relatable
{
    /**
     * The actual object to be saved
     *
     * @var
     */
    public $data;

    /**
     * The relation object
     *
     * @var
     */
    public $relation;

    /**
     * The method name
     *
     * @var
     */
    public $method;

    public function __construct($data, $relation, $method)
    {
        $this->data = $data;
        $this->relation = $relation;
        $this->method = $method;
    }
}