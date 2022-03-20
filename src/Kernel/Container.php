<?php

declare(strict_types=1);

namespace CommissionTask\Kernel;

use CommissionTask\Components\CurrenciesDataReader\ApiCurrenciesDataReader;
use CommissionTask\Components\CurrenciesDataValidator\ApiCurrenciesDataValidator;
use CommissionTask\Components\CurrenciesUpdater\ApiCurrenciesUpdater;
use CommissionTask\Components\Outputter\ConsoleOutputter;
use CommissionTask\Components\Storage\ArrayStorage;
use CommissionTask\Components\TransactionDataValidator\CsvTransactionDataValidator;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\DepositStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\WithdrawBusinessStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\WithdrawPrivateStrategy;
use CommissionTask\Components\TransactionFeeCalculator\TransactionFeeCalculator;
use CommissionTask\Components\TransactionFeeCalculator\TransactionFeeCalculatorStrategyResolver;
use CommissionTask\Components\TransactionSaver\CsvTransactionSaver;
use CommissionTask\Components\TransactionsDataReader\CsvTransactionsDataReader;
use CommissionTask\Exceptions\CommissionTaskKernelException;
use CommissionTask\Repositories\CurrenciesRepository;
use CommissionTask\Repositories\TransactionsRepository;
use CommissionTask\Services\CommandLine as CommandLineService;
use CommissionTask\Services\Config as ConfigService;
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
        $this->put('filesystemService', FilesystemService::getInstance());

        // Put other classes instances
        $this->put('configService', new ConfigService());
        $this->put('commandLineService', new CommandLineService());
        $this->put('mathService', new MathService());
        $this->put('dateService', new DateService());

        // Put data classes
        $this->put('storage', new ArrayStorage());
        $this->put('transactionsRepository', new TransactionsRepository(
            $this->get('storage')
        ));
        $this->put('currenciesRepository', new CurrenciesRepository(
            $this->get('storage')
        ));

        // Put classes of fee calculations instances
        $this->put('currenciesDataReader', new ApiCurrenciesDataReader(
            $this->get('configService')
        ));
        $this->put('currenciesDataValidator', new ApiCurrenciesDataValidator(
            $this->get('configService'),
            $this->get('dateService')
        ));
        $this->put('currenciesUpdater', new ApiCurrenciesUpdater(
            $this->get('currenciesRepository'),
            $this->get('configService'),
            $this->get('mathService')
        ));
        $this->put('currencyService', new CurrencyService(
            $this->get('currenciesRepository'),
            $this->get('currenciesDataReader'),
            $this->get('currenciesDataValidator'),
            $this->get('currenciesUpdater'),
            $this->get('configService'),
            $this->get('mathService')
        ));
        $this->put('depositStrategy', new DepositStrategy(
            $this->get('configService'),
            $this->get('currencyService'),
            $this->get('mathService')
        ));
        $this->put('withdrawPrivateStrategy', new WithdrawPrivateStrategy(
            $this->get('configService'),
            $this->get('currencyService'),
            $this->get('mathService'),
            $this->get('dateService'),
            $this->get('transactionsRepository')
        ));
        $this->put('withdrawBusinessStrategy', new WithdrawBusinessStrategy(
            $this->get('configService'),
            $this->get('currencyService'),
            $this->get('mathService')
        ));
        $this->put('transactionFeeCalculatorStrategyResolver', new TransactionFeeCalculatorStrategyResolver(
            $this->get('depositStrategy'),
            $this->get('withdrawPrivateStrategy'),
            $this->get('withdrawBusinessStrategy')
        ));
        $this->put('transactionFeeCalculator', new TransactionFeeCalculator(
            $this->get('transactionFeeCalculatorStrategyResolver')
        ));

        // Put classes of transactions handling
        $this->put('transactionsDataReader', new CsvTransactionsDataReader(
            $this->get('filesystemService')
        ));
        $this->put('transactionDataValidator', new CsvTransactionDataValidator(
            $this->get('configService'),
            $this->get('dateService'),
            $this->get('currencyService')
        ));
        $this->put('transactionSaver', new CsvTransactionSaver(
            $this->get('transactionsRepository'),
            $this->get('configService'),
            $this->get('dateService')
        ));

        // Put classes for outputting handling
        $this->put('outputter', new ConsoleOutputter());
    }

    /**
     * Get service from service container.
     *
     * @throws CommissionTaskKernelException
     */
    public function get(string $alias): mixed
    {
        if (!$this->isExists($alias)) {
            throw new CommissionTaskKernelException(sprintf(CommissionTaskKernelException::UNDEFINED_SERVICE_MESSAGE, $alias));
        }

        return $this->instances[$alias];
    }

    /**
     * Put service to service container.
     */
    public function put(string $alias, mixed $instance): void
    {
        if (!$this->isExists($alias)) {
            $this->instances[$alias] = $instance;
        }
    }

    /**
     * Check is exists service in service container.
     */
    public function isExists(string $alias): bool
    {
        return isset($this->instances[$alias]);
    }
}
