<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Interfaces;

use CommissionTask\Components\TransactionFeeCalculator\Strategies\Interfaces\TransactionFeeCalculateStrategy;
use CommissionTask\Entities\Transaction;
use CommissionTask\Exceptions\CommissionTaskException;

interface TransactionFeeCalculatorStrategyResolver
{
    /**
     * Resolve transaction fee calculator strategy.
     *
     * @throws CommissionTaskException
     */
    public function getTransactionFeeCalculatorStrategy(Transaction $transaction): TransactionFeeCalculateStrategy;
}
