<?php

declare(strict_types=1);

namespace CommissionTask\Factories\Interfaces;

use CommissionTask\Entities\Transaction;

interface TransactionFactory
{
    /**
     * Make transaction entity.
     *
     * @param mixed $transactionData
     */
    public function makeTransaction($transactionData): Transaction;
}
