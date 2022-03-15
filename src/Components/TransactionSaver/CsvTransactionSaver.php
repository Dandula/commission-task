<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionSaver;

use CommissionTask\Components\TransactionSaver\Interfaces\TransactionSaver as TransactionSaverContract;
use CommissionTask\Factories\Interfaces\TransactionFactory;
use CommissionTask\Repositories\Interfaces\TransactionsRepository;

class CsvTransactionSaver implements TransactionSaverContract
{
    /**
     * Create a new CSV transaction saver instance.
     */
    public function __construct(
        private TransactionsRepository $transactionsRepository,
        private TransactionFactory $transactionFactory
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function saveTransaction(mixed $transactionData): void
    {
        $transaction = $this->transactionFactory->makeTransaction($transactionData);
        $this->transactionsRepository->create($transaction);
    }
}
