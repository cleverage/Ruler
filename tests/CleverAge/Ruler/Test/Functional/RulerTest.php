<?php

namespace CleverAge\Ruler\Test\Functional;

use CleverAge\Ruler\Test\Rule as Example;

class RulerTest extends \PHPUnit_Framework_TestCase
{

    public function testIsSatisfied()
    {
        $rule = new Example\TruelyRule();
        $this->assertTrue($rule->isSatisfied());

        $this->setExpectedException(
          'CleverAge\Ruler\Test\Exception\CustomException',
          'Hello Pony !'
        );

        $rule = new Example\FalsyRule();
        $rule->isSatisfied();
    }

    public function testIsNotSatisfied()
    {
        $rule = new Example\FalsyRule();
        $this->assertTrue($rule->isNotSatisfied());

        $this->setExpectedException(
          'CleverAge\Ruler\Test\Exception\Custom2Exception',
          'Bye Pony !'
        );

        $rule = new Example\TruelyRule();
        $rule->isNotSatisfied();
    }

    public function testCombination1()
    {
        // 1 . 1 . 1
        $rule = new Example\TruelyRule();
        $rule
            ->andRule(new Example\TruelyRule())
            ->andRule(new Example\TruelyRule());
        $this->assertTrue($rule->isSatisfied());

        // 0 . 1 + 1
        $rule2 = new Example\FalsyRule();
        $rule2
            ->orRule(new Example\TruelyRule())
            ->andRule(new Example\TruelyRule());
        $this->assertTrue($rule2->isSatisfied());

        // 1 . 1 + 0
        $rule3 = new Example\TruelyRule();
        $rule3
            ->orRule(new Example\FalsyRule())
            ->andRule(new Example\TruelyRule());
        $this->assertTrue($rule3->isSatisfied());

        // 0 . 0 + 0 + 1
        $rule4 = new Example\FalsyRule();
        $rule4
            ->orRule(new Example\FalsyRule())
            ->orRule(new Example\TruelyRule())
            ->andRule(new Example\FalsyRule());
        $this->assertTrue($rule4->isSatisfied());

        // 1 . !0 + 0
        $rule5 = new Example\TruelyRule();
        $rule5
            ->orRule(new Example\FalsyRule())
            ->nandRule(new Example\FalsyRule());
        $this->assertTrue($rule5->isSatisfied());

        $this->setExpectedException('CleverAge\Ruler\Test\Exception\CustomException');

        // 1 . 1 . 0
        $rule6 = new Example\TruelyRule();
        $rule6
            ->andRule(new Example\TruelyRule())
            ->andRule(new Example\FalsyRule());
        $rule6->isSatisfied();
    }

    /**
     * @expectedException        CleverAge\Ruler\Test\Exception\CustomException
     */
    public function testCombinationFail1()
    {
        // 1 . 0 + 0 + 0
        $rule = new Example\TruelyRule();
        $rule
            ->andRule(new Example\FalsyRule())
            ->orRule(new Example\FalsyRule());
        $rule->isSatisfied();
    }

    /**
     * @expectedException        CleverAge\Ruler\Test\Exception\CustomException
     */
    public function testCombinationFail2()
    {
        // 1 . 0 + 0 . 1 + 0
        $subrule = new Example\TruelyRule();
        $subrule->andRule(new Example\FalsyRule());

        $rule = new Example\TruelyRule();
        $rule
            ->andRule(new Example\FalsyRule())
            ->orRule(new Example\FalsyRule())
            ->orRule($subrule);

        $rule->isSatisfied();
    }

    /**
     * @expectedException        CleverAge\Ruler\Test\Exception\CustomException
     */
    public function testCombinationFail3()
    {
        // 0 . 1 + 1 . !1
        $subrule = new Example\TruelyRule();
        $subrule->nandRule(new Example\TruelyRule());

        $rule = new Example\FalsyRule();
        $rule
            ->andRule(new Example\TruelyRule())
            ->orRule($subrule);

        $rule->isSatisfied();
    }

    /**
     * @expectedException CleverAge\Ruler\Test\Exception\Custom2Exception
     */
    public function testOverrideException()
    {
        $rule = new Example\FalsyRule();
        $rule->setException('CleverAge\Ruler\Test\Exception\Custom2Exception');
        $rule->isSatisfied();
    }
}