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
use CommissionTask\Services\Filesystem as FilesystemService;

class Application
{
    const FILEPATH_PARAMETER_NUMBER = 1;

    /**
     * The base path for the application installation.
     */
    protected $basePath;

    /**
     * The service container for the application.
     */
    protected $container;

    /**
     * Create a new application instance.
     */
    public function __construct(string $basePath, Container $container)
    {
        $this->setContainer($container)
            ->setBasePath($basePath);
    }

    /**
     * Run the application.
     *
     * @return void
     *
     * @throws CommissionTaskThrowable
     */
    public function run()
    {
        $this->initApplication();

        $rawTransactionsData = $this->readRawTransactionsData();

        $this->validateRawTransactionsData($rawTransactionsData);

        $this->saveTransactions($rawTransactionsData);

        $transactionsFees = $this->calculateTransactionsFees();

        $this->output($transactionsFees);
    }

    /**
     * Read raw data of transactions.
     *
     * @return void
     */
    private function initApplication()
    {
        ConfigService::initConfig();
    }

    /**
     * Read raw data of transactions.
     */
    private function readRawTransactionsData(): array
    {
        $commandLineService = $this->container->get(CommandLineService::class);
        $filePath = $commandLineService->getCommandLineParameterByNumber(self::FILEPATH_PARAMETER_NUMBER);

        $transactionsDataReader = $this->container->get(TransactionsDataReader::class);
        $transactionsDataReader->setFilePath($filePath);

        return $transactionsDataReader->readTransactionsData();
    }

    /**
     * Validate raw data of transactions.
     *
     * @return void
     *
     * @throws TransactionDataValidator
     */
    private function validateRawTransactionsData(array $rawTransactionsData)
    {
        $transactionsDataValidator = $this->container->get(TransactionDataValidator::class);

        foreach ($rawTransactionsData as $rawTransactionData) {
            $transactionsDataValidator->validateTransactionData($rawTransactionData);
        }
    }

    /**
     * Save raw data of transactions to transactions entities.
     *
     * @return void
     */
    private function saveTransactions(array $rawTransactionsData)
    {
        $transactionSaver = $this->container->get(TransactionSaver::class);

        foreach ($rawTransactionsData as $rawTransactionDataItem) {
            $transactionSaver->saveTransaction($rawTransactionDataItem);
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
     *
     * @param mixed $outputData
     *
     * @return void
     */
    private function output($outputData)
    {
        $outputter = $this->container->get(Outputter::class);

        $outputter->output($outputData);
    }

    /**
     * Set the base path for the application.
     *
     * @return $this
     */
    private function setBasePath(string $basePath): Application
    {
        $this->basePath = rtrim($basePath, '\/');

        if ($fileSystemService = $this->container->get(FilesystemService::class)) {
            $fileSystemService->setBasePath($this->basePath);
        }

        return $this;
    }

    /**
     * Set the service container for application.
     *
     * @return $this
     */
    private function setContainer(Container $container): Application
    {
        $this->container = $container;

        return $this;
    }
}
