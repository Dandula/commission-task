<?php

declare(strict_types=1);

namespace CommissionTask\Kernel;

use CommissionTask\Entities\Transaction;
use CommissionTask\Exceptions\Interfaces\CommissionTaskThrowable;

class Application
{
    private const FILEPATH_PARAMETER_NUMBER = 1;

    /**
     * The service container for the application.
     */
    private Container $container;

    /**
     * Create a new application instance.
     */
    public function __construct(Container $container)
    {
        $this->setContainer($container);
    }

    /**
     * Run the application.
     *
     * @param string[] $argv
     *
     * @throws CommissionTaskThrowable
     */
    public function run(int $argc, array $argv): void
    {
        $this->initApplication($argc, $argv);

        $this->loadTransactions();

        $transactionsFees = $this->calculateTransactionsFees();

        $this->output($transactionsFees);
    }

    /**
     * Read raw data of transactions.
     *
     * @param string[] $argv
     */
    private function initApplication(int $argc, array $argv): void
    {
        $configService = $this->container->get('configService');
        $configService->initConfig();

        $commandLineService = $this->container->get('commandLineService');
        $commandLineService->initCommandLineParameters($argc, $argv);
    }

    /**
     * Load transactions.
     */
    private function loadTransactions(): void
    {
        $commandLineService = $this->container->get('commandLineService');
        $filePath = $commandLineService->getCommandLineParameterByNumber(self::FILEPATH_PARAMETER_NUMBER);

        $transactionsDataReader = $this->container->get('transactionsDataReader');
        $transactionsDataReader->openFile($filePath);

        $transactionsDataValidator = $this->container->get('transactionDataValidator');

        $transactionSaver = $this->container->get('transactionSaver');

        foreach ($transactionsDataReader->readTransactionsData() as $rawTransactionData) {
            $transactionsDataValidator->validateTransactionData($rawTransactionData);
            $transactionSaver->saveTransaction($rawTransactionData);
        }
    }

    /**
     * Calculate fees of transactions.
     *
     * @return string[]
     */
    private function calculateTransactionsFees(): array
    {
        $transactionsRepository = $this->container->get('transactionsRepository');
        $transactionFeeCalculator = $this->container->get('transactionFeeCalculator');

        $transactionsFees = [];

        /**
         * @var Transaction $transaction
         */
        foreach ($transactionsRepository->all() as $id => $transaction) {
            $fee = $transactionFeeCalculator->calculateTransactionFee($transaction, $id);
            $transactionsFees[] = $fee;
        }

        return $transactionsFees;
    }

    /**
     * Output results of execution.
     */
    private function output(mixed $outputData): void
    {
        $outputter = $this->container->get('outputter');

        $outputter->output($outputData);
    }

    /**
     * Set the service container for application.
     *
     * @return $this
     */
    private function setContainer(Container $container): self
    {
        $this->container = $container;

        return $this;
    }
}
