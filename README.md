Ruler
=====

cleverage/Ruler is a PHP 5.3 Library. Use it to implement your own business rules, and link them in order to check if you are satisfying the set.

Exemple :

You want to check if the current user can activate a feature. For this, you have to check that :
- he is connected,
- he has the premium suscription,
- he has enough money in his account.

```php
<?php // test

// PHP 5.3
$rule = new IsConnectedRule($user);
$rule->andRule(new IsSuscriberRule($user, 'PREMIUM'))
     ->andRule(new HasMoneyRule($user, 300)
     ->orRule(new IsAdminRule($user));

// PHP 5.4
$rule = new IsConnectedRule($user)
  ->andRule(new IsSuscriberRule($user, 'PREMIUM'))
  ->andRule(new HasMoneyRule($user, 300)
  ->orRule(new IsAdminRule($user));

try {
    if ($rule->isSatisfied()) {
      echo 'activated';
    }
} catch (NotConnectedException $e) {
    // show connection form
} catch (NotSuscriberException $e) {
    // show subscription form
} catch (NotEnoughMoneyException $e) {
    echo 'not enough Money';
} catch(\CleverAge\Ruler\Exception\Exception $e) {
    echo 'Failed : '.$e->getMessage();
}
```

```php
<?php // IsConnectedRule class
class IsConnectedRule extends \CleverAge\Ruler\AbstractRule
{
    protected $_user;
  
    protected $_failure_exception_class = 'NotConnectedException';
    protected $_failure_message = 'user is not connected';

    public function __construct(\User $user)
    {
        $this->_user = $user;
    }

    public function doIsSatisfied()
    {
        return $this->_user->isLoggedOn();
    }
}
```

Combination of rules can even be done in a single rule class, in order to simplify your application code and increase maintability.

```php
// ActiveFeatureXRule class
class ActiveFeatureXRule extends \CleverAge\Ruler\AbstractRule
{
    public function __construct(\User $user)
    {
        $this->andRule(new IsSuscriberRule($user, 'PREMIUM'))
             ->andRule(new HasMoneyRule($user, 300)
             ->orRule(new IsAdminRule($user));
    }

    public function doIsSatisfied()
    {
        // method is abstract, and this container rule always satisfies.
        return true;
    }
}
```

Now, you can use this rule class everywhere you need it, and just change the construct to have th rules reverberated everewhere.

## How logic is handled

The order in which you set OR/AND/NAND rules is not important. At the end, they are grouped by type.

1) You want your ruleset satisfied :

```php
// A,B,C,D,G,Z are rules
$A->andRule($B)
  ->orRule($C->andRule($Z))
  ->andRule($D)
  ->nandRule($G)
  ->isSatisfied();

// PHP =>($A && $B && $D && !$G) || ($C && $Z)
// Binary => (A.B.D.!G)+(C.Z)
```

2) You want your ruleset not satisfied :

```php
// A,B,C,D,G,Z are rules
$A->andRule($B)
  ->orRule($C->andRule($Z))
  ->andRule($D)
  ->nandRule($G)
  ->isNotSatisfied()

// PHP => (!$A || !$B || !$D || $G) && (!$C || !$Z)
// Binary => (!A+!B+!D+G).(!C+!Z)
```

by default, isNotSatisfied() returns !isSatisfied(). But sometimes, you may want to personnalize the doIsNotSatisfied() method in order to optimize workflow (like SQL queries).
