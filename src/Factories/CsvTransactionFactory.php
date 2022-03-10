<?php

declare(strict_types=1);

namespace CommissionTask\Factories;

use CommissionTask\Components\DataFormatters\CsvTransactionDataFormatter;
use CommissionTask\Entities\Transaction;
use CommissionTask\Factories\Interfaces\TransactionFactory as TransactionFactoryContract;
use CommissionTask\Services\Date;

class CsvTransactionFactory implements TransactionFactoryContract
{
    /**
     * @var CsvTransactionDataFormatter
     */
    private $csvTransactionDataFormatter;

    /**
     * @var Date
     */
    private $dateService;

    /**
     * Create a new CSV transaction factory instance.
     */
    public function __construct(CsvTransactionDataFormatter $csvTransactionDataFormatter, Date $dateService)
    {
        $this->csvTransactionDataFormatter = $csvTransactionDataFormatter;
        $this->dateService = $dateService;
    }

    /**
     * {@inheritDoc}
     */
    public function makeTransaction($transactionData): Transaction
    {
        $transaction = new Transaction();

        $date = $this->dateService->parseDate(
            $transactionData[$this->csvTransactionDataFormatter::COLUMN_DATE_NUMBER],
            $this->csvTransactionDataFormatter::COLUMN_DATE_FORMAT
        );

        $transaction->setDate($date);
        $transaction->setUserId(intval($transactionData[$this->csvTransactionDataFormatter::COLUMN_USER_ID_NUMBER]));
        $transaction->setUserType($transactionData[$this->csvTransactionDataFormatter::COLUMN_USER_TYPE_NUMBER]);
        $transaction->setType($transactionData[$this->csvTransactionDataFormatter::COLUMN_TYPE_NUMBER]);
        $transaction->setAmount($transactionData[$this->csvTransactionDataFormatter::COLUMN_AMOUNT_NUMBER]);
        $transaction->setCurrencyCode($transactionData[$this->csvTransactionDataFormatter::COLUMN_CURRENCY_CODE_NUMBER]);

        return $transaction;
    }
}
