<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Strategies\Interfaces;

use CommissionTask\Entities\Transaction;

interface TransactionFeeCalculateStrategy
{
    public const NOT_ROUNDED_FRACTIONAL_PART_REGEXP = '/^0+$/';

    /**
     * Calculate transaction fee for given transaction.
     */
    public function calculateTransactionFee(Transaction $transaction, int $id): string;
}
