<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionSaver\Interfaces;

interface TransactionSaver
{
    /**
     * Save transaction to transaction repository.
     */
    public function saveTransaction(mixed $transactionData): void;
}
