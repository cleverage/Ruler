<?php

namespace CleverAge\Ruler;

/**
 * Description of RuleInterface
 *
 * @package CleverAge\Ruler
 * @author Florian Lonqueu-Brochard <flonqueubrochard@clever-age.com>
 * @author Florian Vilpoix <fvilpoix@clever-age.com>
 * @since 2012-10-17
 */
interface RuleInterface
{
    /**
     * Executes the current rule and check that it's satisfied
     *
     * @return boolean true when satisfied, false otherwise
     */
    public function doIsSatisfied();

    /**
     * Executes the current rule and check that it's not satisfied
     *
     * @return boolean true when not satisfied, false otherwise
     */
    public function doIsNotSatisfied();

    /**
     * Executes the current rule, and its and|or associated rules.
     *
     * A->and(B)
     *  ->or(C->and(Z))
     *  ->and(D)
     *  ->nand(G)
     *  ->isSatisfied()
     *
     * => (A.B.D.!G)+(C.Z)
     *
     * @return boolean true when satisfied, throws exception otherwise.
     * @throws \CleverAge\Ruler\Exception\Exception
     */
    public function isSatisfied();

    /**
     * Executes the current rule, and its and|or associated rules. Check that
     * the rule fail.
     *
     * A->and(B)
     *  ->or(C->and(Z))
     *  ->and(D)
     *  ->nand(G)
     *  ->isNotSatisfied()
     *
     * => (!A+!B+!D+G).(!C+!Z)
     *
     * @return boolean true if rule failed, throws exception otherwise
     * @throws \CleverAge\Ruler\Exception\Exception
     */
    public function isNotSatisfied();

    /**
     * Add a sub-rule to current. Both current and new rule must be satisfied
     *
     * @param \CleverAge\Ruler\RuleInterface $rule
     * @return \CleverAge\Ruler\RuleInterface
     */
    public function andRule(RuleInterface $rule);

    /**
     * Add a sub-rule to current.
     * (Current rule and AND rule) or (new OR rule) must satisfied.
     *
     * @param \CleverAge\Ruler\RuleInterface $rule
     * @return \CleverAge\Ruler\RuleInterface
     */
    public function orRule(RuleInterface $rule);

    /**
     * Add a sub-rule to current.
     * rule must not satisfied to have current rule satisfied.
     *
     * @param \CleverAge\Ruler\RuleInterface $rule
     * @return \CleverAge\Ruler\RuleInterface
     */
    public function nandRule(RuleInterface $rule);
}
