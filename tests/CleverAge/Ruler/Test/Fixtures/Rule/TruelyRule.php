<?php

namespace CleverAge\Ruler\Test\Fixtures\Rule;

use CleverAge\Ruler\RuleAbstract;

class TruelyRule extends RuleAbstract
{
    protected $_not_failure_exception_class = 'CleverAge\Ruler\Test\Fixtures\Exception\Custom2Exception';
    protected $_not_failure_message = 'Bye Pony !';

    public function doIsSatisfied()
    {
        return true;
    }
}