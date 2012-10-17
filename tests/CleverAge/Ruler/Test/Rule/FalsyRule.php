<?php

namespace CleverAge\Ruler\Test\Rule;

use CleverAge\Ruler\RuleAbstract;

class FalsyRule extends RuleAbstract
{
    protected $_failure_exception_class = 'CleverAge\Ruler\Test\Exception\CustomException';
    protected $_failure_message = 'Hello Pony !';

    public function doIsSatisfied()
    {
        return false;
    }
}