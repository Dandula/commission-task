<?php

declare(strict_types=1);

namespace CommissionTask\Kernel;

use CommissionTask\Components\CurrenciesDataReader\ApiCurrenciesDataReader;
use CommissionTask\Components\CurrenciesDataReader\Interfaces\CurrenciesDataReader as CurrenciesDataReaderContract;
use CommissionTask\Components\CurrenciesDataValidator\ApiCurrenciesDataValidator;
use CommissionTask\Components\CurrenciesDataValidator\Interfaces\CurrenciesDataValidator as CurrenciesDataValidatorContract;
use CommissionTask\Components\CurrenciesUpdater\ApiCurrenciesUpdater;
use CommissionTask\Components\CurrenciesUpdater\Interfaces\CurrenciesUpdater as CurrenciesUpdaterContract;
use CommissionTask\Components\DataFormatter\ApiCurrenciesDataFormatter;
use CommissionTask\Components\DataFormatter\CsvTransactionDataFormatter;
use CommissionTask\Components\DataFormatter\CurrenciesUpdaterDataFormatter;
use CommissionTask\Components\Outputter\ConsoleOutputter;
use CommissionTask\Components\Outputter\Interfaces\Outputter as OutputterContract;
use CommissionTask\Components\Storage\ArrayStorage;
use CommissionTask\Components\Storage\Interfaces\Storage as StorageContract;
use CommissionTask\Components\TransactionDataValidator\CsvTransactionDataValidator;
use CommissionTask\Components\TransactionDataValidator\Interfaces\TransactionDataValidator as TransactionDataValidatorContract;
use CommissionTask\Components\TransactionFeeCalculator\Interfaces\TransactionFeeCalculator as TransactionFeeCalculatorContract;
use CommissionTask\Components\TransactionFeeCalculator\TransactionFeeCalculator;
use CommissionTask\Components\TransactionSaver\CsvTransactionSaver;
use CommissionTask\Components\TransactionSaver\Interfaces\TransactionSaver as TransactionSaverContract;
use CommissionTask\Components\TransactionsDataReader\CsvTransactionsDataReader;
use CommissionTask\Components\TransactionsDataReader\Interfaces\TransactionsDataReader as TransactionsDataReaderContract;
use CommissionTask\Exceptions\CommissionTaskKernelException;
use CommissionTask\Factories\Interfaces\TransactionFeeCalculatorStrategyFactory as TransactionFeeCalculatorStrategyFactoryContract;
use CommissionTask\Factories\TransactionFeeCalculatorStrategyFactory;
use CommissionTask\Repositories\CurrenciesRepository;
use CommissionTask\Repositories\Interfaces\CurrenciesRepository as CurrenciesRepositoryContract;
use CommissionTask\Repositories\Interfaces\TransactionsRepository as TransactionsRepositoryContract;
use CommissionTask\Repositories\TransactionsRepository;
use CommissionTask\Services\CommandLine as CommandLineService;
use CommissionTask\Services\Currency as CurrencyService;
use CommissionTask\Services\Date as DateService;
use CommissionTask\Services\Filesystem as FilesystemService;

class Container
{
    private array $instances = [];

    /**
     * Init service container.
     */
    public function init(): void
    {
        // Put singletons
        $this->put(FilesystemService::class, FilesystemService::getInstance());

        // Put classes instances
        $this->put(CommandLineService::class, new CommandLineService());
        $this->put(DateService::class, new DateService());
        $this->put(CsvTransactionDataFormatter::class, new CsvTransactionDataFormatter());
        $this->put(ApiCurrenciesDataFormatter::class, new ApiCurrenciesDataFormatter());
        $this->put(CurrenciesUpdaterDataFormatter::class, new CurrenciesUpdaterDataFormatter());

        // Put implemented classes instances
        $this->put(StorageContract::class, new ArrayStorage());
        $this->put(TransactionsRepositoryContract::class, new TransactionsRepository(
            $this->get(StorageContract::class)
        ));
        $this->put(CurrenciesRepositoryContract::class, new CurrenciesRepository(
            $this->get(StorageContract::class)
        ));
        $this->put(TransactionsDataReaderContract::class, new CsvTransactionsDataReader(
            $this->get(FilesystemService::class)
        ));
        $this->put(TransactionDataValidatorContract::class, new CsvTransactionDataValidator(
            $this->get(CsvTransactionDataFormatter::class),
            $this->get(DateService::class)
        ));
        $this->put(TransactionSaverContract::class, new CsvTransactionSaver(
            $this->get(TransactionsRepositoryContract::class),
            $this->get(CsvTransactionDataFormatter::class),
            $this->get(DateService::class)
        ));
        $this->put(CurrenciesDataReaderContract::class, new ApiCurrenciesDataReader());
        $this->put(CurrenciesDataValidatorContract::class, new ApiCurrenciesDataValidator(
            $this->get(ApiCurrenciesDataFormatter::class),
            $this->get(DateService::class)
        ));
        $this->put(CurrenciesUpdaterContract::class, new ApiCurrenciesUpdater(
            $this->get(CurrenciesRepositoryContract::class),
            $this->get(ApiCurrenciesDataFormatter::class),
            $this->get(CurrenciesUpdaterDataFormatter::class),
            $this->get(DateService::class)
        ));

        $this->put(CurrencyService::class, new CurrencyService(
            $this->get(CurrenciesRepositoryContract::class),
            $this->get(CurrenciesDataReaderContract::class),
            $this->get(CurrenciesDataValidatorContract::class),
            $this->get(CurrenciesUpdaterContract::class),
            $this->get(DateService::class)
        ));

        $this->put(TransactionFeeCalculatorStrategyFactoryContract::class, new TransactionFeeCalculatorStrategyFactory(
            $this->get(TransactionsRepositoryContract::class),
            $this->get(DateService::class),
            $this->get(CurrencyService::class)
        ));
        $this->put(TransactionFeeCalculatorContract::class, new TransactionFeeCalculator(
            $this->get(TransactionFeeCalculatorStrategyFactoryContract::class)
        ));
        $this->put(OutputterContract::class, new ConsoleOutputter());
    }

    /**
     * Get service from service container.
     *
     * @throws CommissionTaskKernelException
     */
    public function get(string $alias): mixed
    {
        if (!isset($this->instances[$alias])) {
            throw new CommissionTaskKernelException("Undefined service $alias");
        }

        return $this->instances[$alias];
    }

    /**
     * Put service to service container.
     */
    public function put(string $alias, mixed $instance): void
    {
        $this->instances[$alias] = $instance;
    }
}
