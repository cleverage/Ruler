<?php

namespace CleverAge\Ruler\Test\Fixtures;

class BarObject
{
    protected $id;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}