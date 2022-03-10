<?php

declare(strict_types=1);

namespace CommissionTask\Factories;

use CommissionTask\Components\TransactionFeeCalculator\Strategies\DepositStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\Interfaces\TransactionFeeCalculateStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\WithdrawBusinessStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\WithdrawPrivateStrategy;
use CommissionTask\Entities\Transaction;
use CommissionTask\Factories\Exceptions\TransactionFeeCalculatorStrategyFactoryException;
use CommissionTask\Factories\Interfaces\TransactionFeeCalculatorStrategyFactory as TransactionFeeCalculatorStrategyFactoryContract;
use CommissionTask\Repositories\TransactionsRepository;
use CommissionTask\Services\Currency;
use CommissionTask\Services\Date;

class TransactionFeeCalculatorStrategyFactory implements TransactionFeeCalculatorStrategyFactoryContract
{
    /**
     * @var TransactionsRepository
     */
    private $transactionsRepository;

    /**
     * @var Date
     */
    private $dateService;

    /**
     * @var Currency
     */
    private $currencyService;

    /**
     * Create a new transaction fee calculator strategy factory instance.
     */
    public function __construct(
        TransactionsRepository $transactionsRepository,
        Date $dateService,
        Currency $currencyService
    ) {
        $this->transactionsRepository = $transactionsRepository;
        $this->dateService = $dateService;
        $this->currencyService = $currencyService;
    }

    /**
     * {@inheritDoc}
     */
    public function getTransactionFeeCalculatorStrategy(Transaction $transaction): TransactionFeeCalculateStrategy
    {
        $transactionType = $transaction->getType();

        switch ($transactionType) {
            case Transaction::TYPE_WITHDRAW:
                $transactionUserType = $transaction->getUserType();

                switch ($transactionUserType) {
                    case Transaction::USER_TYPE_PRIVATE:
                        return new WithdrawPrivateStrategy(
                            $this->transactionsRepository,
                            $this->dateService,
                            $this->currencyService
                        );
                    case Transaction::USER_TYPE_BUSINESS:
                        return new WithdrawBusinessStrategy();
                    default:
                        throw new TransactionFeeCalculatorStrategyFactoryException(TransactionFeeCalculatorStrategyFactoryException::UNDEFINED_USER_TYPE_MESSAGE);
                }
                // no break
            case Transaction::TYPE_DEPOSIT:
                return new DepositStrategy();
            default:
                throw new TransactionFeeCalculatorStrategyFactoryException(TransactionFeeCalculatorStrategyFactoryException::UNDEFINED_TRANSACTION_TYPE_MESSAGE);
        }
    }
}
