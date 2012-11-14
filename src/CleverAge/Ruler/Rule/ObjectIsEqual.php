<?php

namespace CleverAge\Ruler\Rule;

use CleverAge\Ruler\RuleAbstract;


/**
 * Description of ObjectIsEqual
 *
 * @author fvilpoix
 */
class ObjectIsEqual extends RuleAbstract
{
    /**
     * @var $object1
     */
    protected $object1;

    /**
     * @var $object2
     */
    protected $object2;

    /**
     * @var string
     */
    protected $identifierMethod;

    public function __construct($object1, $object2 = null, $identifierMethod = 'getId')
    {
        $this->object1 = $object1;
        $this->object2 = $object2;
        $this->identifierMethod = $identifierMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function doIsSatisfied()
    {
        $class = is_object($this->object1) ? get_class($this->object1) : false;

        return $class
            && (($this->object1 == $this->object2)
            || (($this->object2 instanceof $class)
                && ($this->object1->{$this->identifierMethod}()
                    == $this->object2->{$this->identifierMethod}())
            ));
    }
}
