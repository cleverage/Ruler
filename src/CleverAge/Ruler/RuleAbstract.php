<?php

namespace CleverAge\Ruler;

use CleverAge\Ruler\Exception\Exception as RulerException;

/**
 * Description of RuleAbstract
 *
 * @package CleverAge\Ruler
 * @author Florian Lonqueu-Brochard <flonqueubrochard@clever-age.com>
 * @author Florian Vilpoix <fvilpoix@clever-age.com>
 * @since 2012-10-17
 */
abstract class RuleAbstract implements RuleInterface
{
    /**
     * @var string Full qualified class name for the exception to throw when the rule must be satisfied but is not.
     * Must inherit from CleverAge\Ruler\Exception\Exception
     */
    protected $_failure_exception_class = 'CleverAge\Ruler\Exception\NotSatisfiedException';

    /**
     * @var string String to put into the exception thrown when rule is not satisfied when it should.
     * Should be a generic label, then use your translation system to replace it
     * in your application.
     */
    protected $_failure_message;

    /**
     * @var string Full qualified class name for the exception to throw when the rule must not be satisfied but is.
     */
    protected $_not_failure_exception_class = 'CleverAge\Ruler\Exception\NotSatisfiedException';

    /**
     * @var string String to put into the exception thrown when rule is satisfied when it should not.
     * Should be a generic label, then use your translation system to replace it
     * in your application.
     */
    protected $_not_failure_message;

    /**
     * @var array Collection of all OR rules
     */
    protected $_or_children = array();

    /**
     * @var array Collection of all AND rules
     */
    protected $_and_children = array();

    /**
     * @var array Collection of all NAND rules
     */
    protected $_nand_children = array();

    /**
     * {@inheritdoc}
     */
    abstract public function doIsSatisfied();

    /**
     * {@inheritdoc}
     */
    public function doIsNotSatisfied()
    {
        return !$this->doIsSatisfied();
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfied()
    {
        $exception_to_throw = null;

        // first, check that current rule and all AND are satisfied, and NAND not
        // satisfied.
        try {
            if (!$this->doIsSatisfied()) {
                $this->_throwException();
            }

            foreach ($this->_and_children as $child) {
                $child->isSatisfied();
            }

            foreach ($this->_nand_children as $child) {
                $child->isNotSatisfied();
            }
        } catch (RulerException $e) {
            if (is_null($exception_to_throw)) {
                $exception_to_throw = $e;
            }
        }

        // if satisfied, then rule is a success
        if (is_null($exception_to_throw)) {
            return true;
        }

        // if one rule failed before, then check all OR rules. At least one must
        // satisfied in order to validate everything.
        foreach($this->_or_children as $child) {
            try {
                $child->isSatisfied();
                return true;
            } catch (RulerException $e) {
                if (is_null($exception_to_throw)) {
                    $exception_to_throw = $e;
                }
            }
        }

        // If we are here, no rules group has been satisfied, so throws first
        // error found.
        throw $exception_to_throw;
    }

    /**
     * {@inheritdoc}
     */
    public function isNotSatisfied()
    {
        $exception_to_throw = null;

        // first, check that at least one rule bewteen current and AND is not
        // satisfied or that one NAND is satisfied
        try {
            if (!$this->doIsNotSatisfied()) {
                $this->_throwNotException();
            }
        } catch (RulerException $e) {
            if (is_null($exception_to_throw)) {
                $exception_to_throw = $e;
            }
        }

        if (!is_null($exception_to_throw)) {
            foreach ($this->_and_children as $child) {
                try {
                    $child->isNotSatisfied();
                } catch (RulerException $e) {
                    if (is_null($exception_to_throw)) {
                        $exception_to_throw = $e;
                    }
                }
            }
        }

        if (!is_null($exception_to_throw)) {
            foreach ($this->_nand_children as $child) {
                try {
                    $child->isSatisfied();
                } catch (RulerException $e) {
                    if (is_null($exception_to_throw)) {
                        $exception_to_throw = $e;
                    }
                }
            }
        }

        // if no rule satisfied, then rule is failure
        if (!is_null($exception_to_throw)) {
            throw $exception_to_throw;
        }

        // All OR rules must not satisfied
        foreach($this->_or_children as $child) {
            $child->isNotSatisfied();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function andRule(RuleInterface $rule)
    {
        $this->_and_children[] = $rule;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function orRule(RuleInterface $rule)
    {
        $this->_or_children[] = $rule;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function nandRule(RuleInterface $rule)
    {
        $this->_nand_children[] = $rule;
        return $this;
    }

    /**
     * @throws CleverAge\Ruler\Exception\Exception
     */
    protected function _throwException()
    {
        $class = $this->_failure_exception_class;
        throw new $class($this->_failure_message);
    }

    /**
     * @throws CleverAge\Ruler\Exception\Exception
     */
    protected function _throwNotException()
    {
        $class = $this->_not_failure_exception_class;
        throw new $class($this->_not_failure_message);
    }

    /**
     * Override the default class used to generate error.
     * Use this if you want to use the same Rule but to generate differents
     * errors.
     *
     * @param string $exceptionClass The Full qualified name of the classe. Must inherit from CleverAge\Ruler\Exception\Exception
     * @param string $msg            The message returned by the exception
     *
     * @return \CleverAge\Ruler\RuleInterface
     */
    public function setException($exceptionClass, $msg = null)
    {
        $this->_failure_exception_class = $exceptionClass;
        $this->_failure_message = $msg;

        return $this;
    }

    /**
     * @see self::setException()
     *
     * @param string $exceptionClass
     * @param string $msg
     *
     * @return \CleverAge\Ruler\RuleInterface
     */
    public function setNotException($exceptionClass, $msg = null)
    {
        $this->_not_failure_exception_class = $exceptionClass;
        $this->_not_failure_message = $msg;

        return $this;
    }
}
