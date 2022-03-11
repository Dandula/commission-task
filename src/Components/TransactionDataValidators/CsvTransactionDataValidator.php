<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidators;

use CommissionTask\Components\DataFormatters\CsvTransactionDataFormatter;
use CommissionTask\Components\TransactionDataValidators\Exceptions\TransactionDataValidatorException;
use CommissionTask\Components\TransactionDataValidators\Interfaces\TransactionDataValidator as TransactionDataValidatorContract;
use CommissionTask\Components\TransactionDataValidators\Traits\FieldFormat;
use CommissionTask\Entities\Transaction;
use CommissionTask\Services\Date as DateService;

class CsvTransactionDataValidator implements TransactionDataValidatorContract
{
    use FieldFormat;

    /**
     * @var CsvTransactionDataFormatter
     */
    private $csvTransactionDataFormatter;

    /**
     * @var DateService
     */
    private $dateService;

    /**
     * @var array
     */
    private $validatedData;

    /**
     * Create CSV transaction data validator instance.
     */
    public function __construct(CsvTransactionDataFormatter $csvTransactionDataFormatter, DateService $dateService)
    {
        $this->csvTransactionDataFormatter = $csvTransactionDataFormatter;
        $this->dateService = $dateService;
    }

    /**
     * {@inheritDoc}
     */
    public function validateTransactionData(array $transactionData)
    {
        $this->validatedData = $transactionData;

        $this->validateColumnsNumber()
            ->validateDate()
            ->validateUserId()
            ->validateUserType()
            ->validateType()
            ->validateAmount()
            ->validateCurrencyCode();
    }

    /**
     * Validate fields number.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateColumnsNumber(): CsvTransactionDataValidator
    {
        if (count($this->validatedData) !== $this->csvTransactionDataFormatter::COLUMNS_NUMBER) {
            throw new TransactionDataValidatorException(TransactionDataValidatorException::INCORRECT_FIELDS_NUMBER_MESSAGE);
        }

        return $this;
    }

    /**
     * Validate date column.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateDate(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet($this->csvTransactionDataFormatter::COLUMN_DATE_NUMBER)
            ->validateDateField(
                $this->validatedData[$this->csvTransactionDataFormatter::COLUMN_DATE_NUMBER],
                $this->csvTransactionDataFormatter::COLUMN_DATE_FORMAT
            );
    }

    /**
     * Validate user ID column.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateUserId(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet($this->csvTransactionDataFormatter::COLUMN_USER_ID_NUMBER)
            ->validateUnsignedIntegerField($this->validatedData[$this->csvTransactionDataFormatter::COLUMN_USER_ID_NUMBER]);
    }

    /**
     * Validate user type column.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateUserType(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet($this->csvTransactionDataFormatter::COLUMN_USER_TYPE_NUMBER)
            ->validateInArrayField(
                $this->validatedData[$this->csvTransactionDataFormatter::COLUMN_USER_TYPE_NUMBER],
                [Transaction::USER_TYPE_PRIVATE, Transaction::USER_TYPE_BUSINESS]
            );
    }

    /**
     * Validate type column.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateType(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet($this->csvTransactionDataFormatter::COLUMN_TYPE_NUMBER)
            ->validateInArrayField(
                $this->validatedData[$this->csvTransactionDataFormatter::COLUMN_TYPE_NUMBER],
                [Transaction::TYPE_DEPOSIT, Transaction::TYPE_WITHDRAW]
            );
    }

    /**
     * Validate amount column.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateAmount(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet($this->csvTransactionDataFormatter::COLUMN_AMOUNT_NUMBER)
            ->validateUnsignedFloatField($this->validatedData[$this->csvTransactionDataFormatter::COLUMN_AMOUNT_NUMBER]);
    }

    /**
     * Validate currency code column.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateCurrencyCode(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet($this->csvTransactionDataFormatter::COLUMN_CURRENCY_CODE_NUMBER)
            ->validateCurrencyCodeField($this->validatedData[$this->csvTransactionDataFormatter::COLUMN_CURRENCY_CODE_NUMBER]);
    }

    /**
     * Validate column is set.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateColumnSet(int $columnNumber): CsvTransactionDataValidator
    {
        if (empty($this->validatedData[$columnNumber])) {
            throw new TransactionDataValidatorException(TransactionDataValidatorException::REQUIRED_FIELD_NOT_SET_MESSAGE);
        }

        return $this;
    }
}
