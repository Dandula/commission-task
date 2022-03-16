<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator;

use CommissionTask\Components\TransactionFeeCalculator\Interfaces\TransactionFeeCalculatorStrategyResolver as TransactionFeeCalculatorStrategyResolverContract;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\DepositStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\Interfaces\TransactionFeeCalculateStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\WithdrawBusinessStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\WithdrawPrivateStrategy;
use CommissionTask\Entities\Transaction;
use CommissionTask\Exceptions\CommissionTaskException;

class TransactionFeeCalculatorStrategyResolver implements TransactionFeeCalculatorStrategyResolverContract
{
    /**
     * Create a new transaction fee calculator strategy factory instance.
     */
    public function __construct(
        private DepositStrategy $depositStrategy,
        private WithdrawPrivateStrategy $withdrawPrivateStrategy,
        private WithdrawBusinessStrategy $withdrawBusinessStrategy
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
                    Transaction::USER_TYPE_PRIVATE => $this->withdrawPrivateStrategy,
                    Transaction::USER_TYPE_BUSINESS => $this->withdrawBusinessStrategy,
                    default => throw new CommissionTaskException(CommissionTaskException::UNDEFINED_USER_TYPE_MESSAGE),
                };
                // no break
            case Transaction::TYPE_DEPOSIT:
                return $this->depositStrategy;
            default:
                throw new CommissionTaskException(CommissionTaskException::UNDEFINED_TRANSACTION_TYPE_MESSAGE);
        }
    }
}
