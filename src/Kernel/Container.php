<?php

declare(strict_types=1);

namespace CommissionTask\Kernel;

use CommissionTask\Components\DataFormatters\CsvTransactionDataFormatter;
use CommissionTask\Components\Storage\ArrayStorage;
use CommissionTask\Components\Storage\Interfaces\Storage as StorageContract;
use CommissionTask\Components\TransactionDataValidators\CsvTransactionDataValidator;
use CommissionTask\Components\TransactionDataValidators\Interfaces\TransactionDataValidator as TransactionDataValidatorContract;
use CommissionTask\Components\TransactionFeeCalculator\Interfaces\TransactionFeeCalculator as TransactionFeeCalculatorContract;
use CommissionTask\Components\TransactionFeeCalculator\TransactionFeeCalculator;
use CommissionTask\Components\TransactionSaver\CsvTransactionSaver;
use CommissionTask\Components\TransactionSaver\Interfaces\TransactionSaver as TransactionSaverContract;
use CommissionTask\Components\TransactionsDataReaders\CsvTransactionsDataReader;
use CommissionTask\Components\TransactionsDataReaders\Interfaces\TransactionsDataReader as TransactionsDataReaderContract;
use CommissionTask\Exceptions\CommissionTaskKernelException;
use CommissionTask\Factories\CsvTransactionFactory;
use CommissionTask\Factories\Interfaces\TransactionFactory as TransactionFactoryContract;
use CommissionTask\Factories\Interfaces\TransactionFeeCalculatorStrategyFactory as TransactionFeeCalculatorStrategyFactoryContract;
use CommissionTask\Factories\TransactionFeeCalculatorStrategyFactory;
use CommissionTask\Repositories\Interfaces\TransactionsRepository as TransactionsRepositoryContract;
use CommissionTask\Repositories\TransactionsRepository;
use CommissionTask\Services\CommandLine;
use CommissionTask\Services\Currency;
use CommissionTask\Services\Date;
use CommissionTask\Services\Filesystem;

class Container
{
    private $instances = [];

    /**
     * Init service container.
     *
     * @return void
     * @throws CommissionTaskKernelException
     */
    public function init()
    {
        // Put singletons
        $this->put(Filesystem::class, Filesystem::getInstance());

        // Put classes instances
        $this->put(CommandLine::class, new CommandLine());
        $this->put(Date::class, new Date());
        $this->put(Currency::class, new Currency());
        $this->put(CsvTransactionDataFormatter::class, new CsvTransactionDataFormatter());

        // Put implemented classes instances
        $this->put(StorageContract::class, new ArrayStorage());
        $this->put(TransactionsRepositoryContract::class, new TransactionsRepository(
            $this->get(StorageContract::class)
        ));
        $this->put(TransactionFactoryContract::class, new CsvTransactionFactory(
            $this->get(CsvTransactionDataFormatter::class),
            $this->get(Date::class)
        ));
        $this->put(TransactionFeeCalculatorStrategyFactoryContract::class, new TransactionFeeCalculatorStrategyFactory(
            $this->get(TransactionsRepositoryContract::class),
            $this->get(Date::class),
            $this->get(Currency::class)
        ));
        $this->put(TransactionsDataReaderContract::class, new CsvTransactionsDataReader(
            $this->get(Filesystem::class)
        ));
        $this->put(TransactionDataValidatorContract::class, new CsvTransactionDataValidator(
            $this->get(CsvTransactionDataFormatter::class),
            $this->get(Date::class)
        ));
        $this->put(TransactionSaverContract::class, new CsvTransactionSaver(
            $this->get(TransactionsRepositoryContract::class),
            $this->get(TransactionFactoryContract::class)
        ));
        $this->put(TransactionFeeCalculatorContract::class, new TransactionFeeCalculator(
            $this->get(TransactionFeeCalculatorStrategyFactoryContract::class)
        ));
    }

    /**
     * Get service from service container.
     *
     * @return mixed
     * @throws CommissionTaskKernelException
     */
    public function get(string $alias)
    {
        if (!isset($this->instances[$alias])) {
            throw new CommissionTaskKernelException("Undefined service $alias");
        }

        return $this->instances[$alias];
    }

    /**
     * Put service to service container.
     *
     * @param mixed $instance
     * @return void
     */
    public function put(string $alias, $instance)
    {
        $this->instances[$alias] = $instance;
    }
}
