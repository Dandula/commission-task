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
use CommissionTask\Components\Outputter\ConsoleOutputter;
use CommissionTask\Components\Outputter\Interfaces\Outputter as OutputterContract;
use CommissionTask\Components\Storage\ArrayStorage;
use CommissionTask\Components\Storage\Interfaces\Storage as StorageContract;
use CommissionTask\Components\TransactionDataValidator\CsvTransactionDataValidator;
use CommissionTask\Components\TransactionDataValidator\Interfaces\TransactionDataValidator as TransactionDataValidatorContract;
use CommissionTask\Components\TransactionFeeCalculator\Interfaces\TransactionFeeCalculator as TransactionFeeCalculatorContract;
use CommissionTask\Components\TransactionFeeCalculator\Interfaces\TransactionFeeCalculatorStrategyResolver as TransactionFeeCalculatorStrategyResolverContract;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\DepositStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\WithdrawBusinessStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\WithdrawPrivateStrategy;
use CommissionTask\Components\TransactionFeeCalculator\TransactionFeeCalculator;
use CommissionTask\Components\TransactionFeeCalculator\TransactionFeeCalculatorStrategyResolver;
use CommissionTask\Components\TransactionSaver\CsvTransactionSaver;
use CommissionTask\Components\TransactionSaver\Interfaces\TransactionSaver as TransactionSaverContract;
use CommissionTask\Components\TransactionsDataReader\CsvTransactionsDataReader;
use CommissionTask\Components\TransactionsDataReader\Interfaces\TransactionsDataReader as TransactionsDataReaderContract;
use CommissionTask\Exceptions\CommissionTaskKernelException;
use CommissionTask\Repositories\CurrenciesRepository;
use CommissionTask\Repositories\Interfaces\CurrenciesRepository as CurrenciesRepositoryContract;
use CommissionTask\Repositories\Interfaces\TransactionsRepository as TransactionsRepositoryContract;
use CommissionTask\Repositories\TransactionsRepository;
use CommissionTask\Services\CommandLine as CommandLineService;
use CommissionTask\Services\Currency as CurrencyService;
use CommissionTask\Services\Date as DateService;
use CommissionTask\Services\Filesystem as FilesystemService;
use CommissionTask\Services\Math as MathService;

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

        // Put other classes instances
        $this->put(CommandLineService::class, new CommandLineService());
        $this->put(MathService::class, new MathService());
        $this->put(DateService::class, new DateService());
        $this->put(CsvTransactionDataFormatter::class, new CsvTransactionDataFormatter());
        $this->put(ApiCurrenciesDataFormatter::class, new ApiCurrenciesDataFormatter());

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
            $this->get(ApiCurrenciesDataFormatter::class)
        ));

        // Put classes of fee calculations instances
        $this->put(CurrencyService::class, new CurrencyService(
            $this->get(CurrenciesRepositoryContract::class),
            $this->get(CurrenciesDataReaderContract::class),
            $this->get(CurrenciesDataValidatorContract::class),
            $this->get(CurrenciesUpdaterContract::class),
            $this->get(MathService::class)
        ));
        $this->put(DepositStrategy::class, new DepositStrategy(
            $this->get(MathService::class)
        ));
        $this->put(WithdrawPrivateStrategy::class, new WithdrawPrivateStrategy(
            $this->get(TransactionsRepositoryContract::class),
            $this->get(MathService::class),
            $this->get(DateService::class),
            $this->get(CurrencyService::class)
        ));
        $this->put(WithdrawBusinessStrategy::class, new WithdrawBusinessStrategy(
            $this->get(MathService::class)
        ));
        $this->put(TransactionFeeCalculatorStrategyResolverContract::class, new TransactionFeeCalculatorStrategyResolver(
            $this->get(DepositStrategy::class),
            $this->get(WithdrawPrivateStrategy::class),
            $this->get(WithdrawBusinessStrategy::class)
        ));
        $this->put(TransactionFeeCalculatorContract::class, new TransactionFeeCalculator(
            $this->get(TransactionFeeCalculatorStrategyResolverContract::class)
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
