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
    $app = new Application(
        realpath(__DIR__),
        $container
    );

    $app->run();

    $exitCode = 0;
} catch (\Throwable $exception) {
    echo $exception->getTraceAsString().PHP_EOL;

    $exitCode = $exception->getCode();
} finally {
    exit($exitCode);
}
