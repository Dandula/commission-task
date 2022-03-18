<?php

declare(strict_types=1);

namespace CommissionTask\Kernel;

use CommissionTask\Components\Outputter\Interfaces\Outputter;
use CommissionTask\Components\TransactionDataValidator\Interfaces\TransactionDataValidator;
use CommissionTask\Components\TransactionFeeCalculator\Interfaces\TransactionFeeCalculator;
use CommissionTask\Components\TransactionSaver\Interfaces\TransactionSaver;
use CommissionTask\Components\TransactionsDataReader\Interfaces\TransactionsDataReader;
use CommissionTask\Entities\Transaction;
use CommissionTask\Exceptions\Interfaces\CommissionTaskThrowable;
use CommissionTask\Repositories\Interfaces\TransactionsRepository;
use CommissionTask\Services\CommandLine as CommandLineService;
use CommissionTask\Services\Config as ConfigService;

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
        $commandLineService = $this->container->get(ConfigService::class);
        $commandLineService->initConfig();

        $commandLineService = $this->container->get(CommandLineService::class);
        $commandLineService->initCommandLineParameters($argc, $argv);
    }

    /**
     * Load transactions.
     */
    private function loadTransactions(): void
    {
        $commandLineService = $this->container->get(CommandLineService::class);
        $filePath = $commandLineService->getCommandLineParameterByNumber(self::FILEPATH_PARAMETER_NUMBER);

        $transactionsDataReader = $this->container->get(TransactionsDataReader::class);
        $transactionsDataReader->openFile($filePath);

        $transactionsDataValidator = $this->container->get(TransactionDataValidator::class);

        $transactionSaver = $this->container->get(TransactionSaver::class);

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
        $transactionsRepository = $this->container->get(TransactionsRepository::class);
        $transactionFeeCalculator = $this->container->get(TransactionFeeCalculator::class);

        $transactionsFees = [];

        /**
         * @var Transaction $transaction
         */
        foreach ($transactionsRepository->all() as $transaction) {
            $transactionsFees[] = $transactionFeeCalculator->calculateTransactionFee($transaction);
        }

        return $transactionsFees;
    }

    /**
     * Output results of execution.
     */
    private function output(mixed $outputData): void
    {
        $outputter = $this->container->get(Outputter::class);

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
