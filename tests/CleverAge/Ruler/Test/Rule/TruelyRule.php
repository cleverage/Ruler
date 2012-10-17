<?php

namespace CleverAge\Ruler\Test\Rule;

use CleverAge\Ruler\RuleAbstract;

class TruelyRule extends RuleAbstract
{
    protected $_not_failure_exception_class = 'CleverAge\Ruler\Test\Exception\Custom2Exception';
    protected $_not_failure_message = 'Bye Pony !';

    public function doIsSatisfied()
    {
        return true;
    }
}