<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Interfaces;

use CommissionTask\Components\TransactionFeeCalculator\Exceptions\TransactionFeeCalculatorLogicException;
use CommissionTask\Entities\Transaction;

interface TransactionFeeCalculator
{
    /**
     * Calculate transaction fee for given transaction.
     *
     * @throws TransactionFeeCalculatorLogicException
     */
    public function calculateTransactionFee(Transaction $transaction, int $id): string;
}
