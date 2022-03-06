<?php

declare(strict_types=1);

namespace CommissionTask\Factories\Interfaces;

use CommissionTask\Components\TransactionFeeCalculator\Strategies\Interfaces\TransactionFeeCalculateStrategy;
use CommissionTask\Entities\Transaction;
use CommissionTask\Factories\Exceptions\TransactionFeeCalculatorStrategyFactoryException;

interface TransactionFeeCalculatorStrategyFactory
{
    /**
     * Get transaction fee calculator strategy.
     *
     * @throws TransactionFeeCalculatorStrategyFactoryException
     */
    public function getTransactionFeeCalculatorStrategy(Transaction $transaction): TransactionFeeCalculateStrategy;
}
