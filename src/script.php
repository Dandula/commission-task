<?php

declare(strict_types=1);

use CommissionTask\Exceptions\Interfaces\CommissionTaskThrowable;
use CommissionTask\Kernel\Application;
use CommissionTask\Kernel\Container;

// Register the Auto Loader
require __DIR__.'/../vendor/autoload.php';

try {
    $container = new Container();
    $container->init();

    // Create the Application
    $app = new Application($container);

    $app->run($argc, $argv);

    $exitCode = 0;
} catch (CommissionTaskThrowable $exception) {
    echo $exception->getMessage().PHP_EOL;

    $exitCode = $exception->getCode();
} finally {
    exit($exitCode);
}
