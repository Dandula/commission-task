<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionSaver;

use CommissionTask\Components\TransactionSaver\Interfaces\TransactionSaver as TransactionSaverContract;
use CommissionTask\Entities\Transaction;
use CommissionTask\Repositories\Interfaces\TransactionsRepository;
use CommissionTask\Services\Config as ConfigService;
use CommissionTask\Services\Date as DateService;

class CsvTransactionSaver implements TransactionSaverContract
{
    /**
     * Create a new CSV transaction saver instance.
     */
    public function __construct(
        private TransactionsRepository $transactionsRepository,
        private DateService $dateService
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function saveTransaction(mixed $transactionData): void
    {
        $transaction = $this->makeTransaction($transactionData);
        $this->transactionsRepository->create($transaction);
    }

    /**
     * Create transaction entity instance.
     */
    private function makeTransaction(mixed $transactionData): Transaction
    {
        $date = $this->dateService->parseDate(
            $transactionData[$this->getColumnNumber('date')],
            ConfigService::getConfigByName('transactionsCsv.dateFormat')
        );

        return new Transaction(
            $date,
            (int) $transactionData[$this->getColumnNumber('userId')],
            $transactionData[$this->getColumnNumber('userType')],
            $transactionData[$this->getColumnNumber('type')],
            $transactionData[$this->getColumnNumber('amount')],
            $transactionData[$this->getColumnNumber('currencyCode')]
        );
    }

    /**
     * Get column number.
     */
    private function getColumnNumber(string $column): int
    {
        return ConfigService::getConfigByName('transactionsCsv.columnsNumbers.'.$column);
    }
}
