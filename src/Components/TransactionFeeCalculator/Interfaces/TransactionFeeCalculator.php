<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Interfaces;

use CommissionTask\Components\TransactionFeeCalculator\Exceptions\Interfaces\TransactionFeeCalculatorException;
use CommissionTask\Entities\Transaction;

interface TransactionFeeCalculator
{
    /**
     * Calculate transaction fee for given transaction.
     *
     * @throws TransactionFeeCalculatorException
     */
    public function calculateTransactionFee(Transaction $transaction): string;
}
