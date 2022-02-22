<?php

declare(strict_types=1);

// Register the Auto Loader
require __DIR__ . '/../vendor/autoload.php';

// Create the Application
$app = new CommissionTask\Kernel\Application(
    realpath(__DIR__)
);

try {
    $transactionsData = $app->readTransactionsData();

    $app->validateTransactionsData($transactionsData);

    $transactionsFees = $app->run($transactionsData);

    $app->output($transactionsFees);
} catch (\CommissionTask\Exceptions\CommissionTaskException $exception) {
    $app->output($exception->getMessage());
    exit($exception->getCode());
}

exit(0);
