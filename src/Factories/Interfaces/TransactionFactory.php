<?php

declare(strict_types=1);

namespace CommissionTask\Factories\Interfaces;

use CommissionTask\Entities\Transaction;

interface TransactionFactory
{
    /**
     * Make transaction entity.
     */
    public function makeTransaction(mixed $transactionData): Transaction;
}
