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
use CommissionTask\Services\Currency as CurrencyService;
use CommissionTask\Services\Date as DateService;

class TransactionFeeCalculatorStrategyFactory implements TransactionFeeCalculatorStrategyFactoryContract
{
    /**
     * Create a new transaction fee calculator strategy factory instance.
     */
    public function __construct(
        private TransactionsRepository $transactionsRepository,
        private DateService $dateService,
        private CurrencyService $currencyService
    ) {
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

                return match ($transactionUserType) {
                    Transaction::USER_TYPE_PRIVATE => new WithdrawPrivateStrategy(
                        $this->transactionsRepository,
                        $this->dateService,
                        $this->currencyService
                    ),
                    Transaction::USER_TYPE_BUSINESS => new WithdrawBusinessStrategy(),
                    default => throw new TransactionFeeCalculatorStrategyFactoryException(TransactionFeeCalculatorStrategyFactoryException::UNDEFINED_USER_TYPE_MESSAGE),
                };
                // no break
            case Transaction::TYPE_DEPOSIT:
                return new DepositStrategy();
            default:
                throw new TransactionFeeCalculatorStrategyFactoryException(TransactionFeeCalculatorStrategyFactoryException::UNDEFINED_TRANSACTION_TYPE_MESSAGE);
        }
    }
}
