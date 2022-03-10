<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionSaver\Interfaces;

interface TransactionSaver
{
    /**
     * Save transaction to transaction repository.
     *
     * @param mixed $transactionData
     *
     * @return void
     */
    public function saveTransaction($transactionData);
}
