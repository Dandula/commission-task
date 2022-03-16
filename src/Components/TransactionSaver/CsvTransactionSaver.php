<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionSaver;

use CommissionTask\Components\DataFormatter\CsvTransactionDataFormatter;
use CommissionTask\Components\TransactionSaver\Interfaces\TransactionSaver as TransactionSaverContract;
use CommissionTask\Entities\Transaction;
use CommissionTask\Repositories\Interfaces\TransactionsRepository;
use CommissionTask\Services\Date as DateService;

class CsvTransactionSaver implements TransactionSaverContract
{
    /**
     * Create a new CSV transaction saver instance.
     */
    public function __construct(
        private TransactionsRepository $transactionsRepository,
        private CsvTransactionDataFormatter $csvTransactionDataFormatter,
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
            $transactionData[$this->csvTransactionDataFormatter::COLUMN_DATE_NUMBER],
            $this->csvTransactionDataFormatter::COLUMN_DATE_FORMAT
        );

        return new Transaction(
            $date,
            (int) $transactionData[$this->csvTransactionDataFormatter::COLUMN_USER_ID_NUMBER],
            $transactionData[$this->csvTransactionDataFormatter::COLUMN_USER_TYPE_NUMBER],
            $transactionData[$this->csvTransactionDataFormatter::COLUMN_TYPE_NUMBER],
            $transactionData[$this->csvTransactionDataFormatter::COLUMN_AMOUNT_NUMBER],
            $transactionData[$this->csvTransactionDataFormatter::COLUMN_CURRENCY_CODE_NUMBER]
        );
    }
}
