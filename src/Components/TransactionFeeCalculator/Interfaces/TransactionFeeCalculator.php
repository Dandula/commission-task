<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Interfaces;

use CommissionTask\Entities\Transaction;

interface TransactionFeeCalculator
{
    /**
     * Calculate transaction fee for given transaction.
     */
    public function calculateTransactionFee(Transaction $transaction): string;
}
