<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionSaver;

use CommissionTask\Components\TransactionSaver\Interfaces\TransactionSaver as TransactionSaverContract;
use CommissionTask\Factories\Interfaces\TransactionFactory;
use CommissionTask\Repositories\Interfaces\TransactionsRepository;

class CsvTransactionSaver implements TransactionSaverContract
{
    /**
     * @var TransactionsRepository
     */
    private $transactionsRepository;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * Create a new CSV transaction saver instance.
     */
    public function __construct(
        TransactionsRepository $transactionRepository,
        TransactionFactory $transactionFactory
    )
    {
        $this->transactionsRepository = $transactionRepository;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @inheritDoc
     */
    public function saveTransaction($transactionData)
    {
        $transaction = $this->transactionFactory->makeTransaction($transactionData);
        $this->transactionsRepository->create($transaction);
    }
}
