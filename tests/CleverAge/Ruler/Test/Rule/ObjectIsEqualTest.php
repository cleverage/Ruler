<?php

namespace CleverAge\Ruler\Test\Rule;

use CleverAge\Ruler\Rule\ObjectIsEqual;
use CleverAge\Ruler\Test\Fixtures\FooObject as Foo;
use CleverAge\Ruler\Test\Fixtures\BarObject as Bar;
use CleverAge\Ruler\Test\Fixtures\FooChildObject as FooChild;

/**
 * @author FlorianLB
 */
class ObjectIsEqualTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider satifyingRules
     * @test
     */
    public function it_should_be_satisfied($satisfyingRule)
    {
        $this->assertTrue($satisfyingRule->isSatisfied());
    }

    public function satifyingRules()
    {
        $foo = new Foo();

        return array(
            array(new ObjectIsEqual($foo, $foo)),
            array(new ObjectIsEqual($foo, $foo->setId(1))),
            array(new ObjectIsEqual(new Foo(2), new Foo(2))),
            array(new ObjectIsEqual(new Foo(2), new FooChild(2))),
        );
    }

    /**
     * @dataProvider unsatifyingRules
     * @test
     */
    public function it_should_not_be_satisfied($unsatisfyingRule)
    {
        $this->setExpectedException('CleverAge\Ruler\Exception\NotSatisfiedException');

        $unsatisfyingRule->isSatisfied();
    }

    public function unsatifyingRules()
    {
        return array(
            array(new ObjectIsEqual(new Foo(), new Bar())),
            array(new ObjectIsEqual(new Foo(2), new Bar(2))),
            array(new ObjectIsEqual(1, new Bar())),
            array(new ObjectIsEqual(new Foo(), 'pony'))
        );
    }

    /**
     * @test
     */
    public function it_should_be_instantiable()
    {
        $this->assertInstanceOf(
            'CleverAge\Ruler\Rule\ObjectIsEqual',
            new ObjectIsEqual(new Foo(), new Bar(), 'getName')
        );
    }
}
