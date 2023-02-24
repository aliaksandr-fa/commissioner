<?php

use Commissioner\Commissioner;
use Commissioner\Transaction\Presenter\ConsoleCommissionsPresenter;
use Psr\Container\ContainerInterface;

require_once dirname(__DIR__).'/config/bootstrap.php';

/** @var ContainerInterface $container */
$container = require_once dirname(__DIR__).'/config/di.php';

try
{
    $commissioner = $container->get(Commissioner::class);
    $commissions  = $commissioner->charge();

    $container
        ->get(ConsoleCommissionsPresenter::class)
        ->present($commissions)
    ;
}
catch (\Exception $e)
{
    echo "An error occured: \n";
    echo "\t{$e->getMessage()}\n";
    echo "\t{$e->getTraceAsString()}\n";
}


