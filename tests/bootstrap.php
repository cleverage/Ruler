<?php

if (!$loader = @include __DIR__.'/../vendor/autoload.php') {
    echo <<<EOM
You must set up the project autoloader by running the following commands:

curl -s http://getcomposer.org/installer | php
php composer.phar install

EOM;

    exit(1);
}

$loader->add('CleverAge\Ruler\Test', __DIR__);
$loader->register();