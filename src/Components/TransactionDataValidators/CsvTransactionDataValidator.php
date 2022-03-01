<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidators;

use CommissionTask\Components\DataFormatters\CsvTransactionDataFormatter;
use CommissionTask\Components\TransactionDataValidators\Exceptions\IncorrectFieldFormat;
use CommissionTask\Components\TransactionDataValidators\Exceptions\IncorrectFieldsNumber;
use CommissionTask\Components\TransactionDataValidators\Exceptions\RequiredFieldNotSet;
use CommissionTask\Components\TransactionDataValidators\Interfaces\TransactionDataValidator as TransactionDataValidatorContract;
use CommissionTask\Components\TransactionDataValidators\Traits\FieldFormat;
use CommissionTask\Services\Date;

class CsvTransactionDataValidator implements TransactionDataValidatorContract
{
    use FieldFormat;

    /**
     * @var CsvTransactionDataFormatter
     */
    private $csvTransactionDataFormatter;

    /**
     * @var Date
     */
    private $dateService;

    /**
     * @var array
     */
    private $validatedData;

    /**
     * Create validator instance.
     */
    public function __construct(CsvTransactionDataFormatter $csvTransactionDataFormatter, Date $dateService)
    {
        $this->csvTransactionDataFormatter = $csvTransactionDataFormatter;
        $this->dateService = $dateService;
    }

    /**
     * @inheritDoc
     */
    public function validateTransactionData(array $transactionData)
    {
        $this->validatedData = $transactionData;

        $this->validateColumnsCount()
            ->validateDate()
            ->validateUserId()
            ->validateUserType()
            ->validateType()
            ->validateAmount()
            ->validateCurrencyCode();
    }

    /**
     * Validate fields count.
     *
     * @return $this
     * @throws IncorrectFieldsNumber
     */
    private function validateColumnsCount(): CsvTransactionDataValidator
    {
        if (count($this->validatedData) !== $this->csvTransactionDataFormatter::COLUMNS_NUMBER) {
            throw new IncorrectFieldsNumber('Incorrect number of columns in the transaction data');
        }

        return $this;
    }

    /**
     * Validate date column.
     *
     * @return $this
     * @throws RequiredFieldNotSet|IncorrectFieldFormat
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
     * @throws RequiredFieldNotSet|IncorrectFieldFormat
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
     * @throws RequiredFieldNotSet|IncorrectFieldFormat
     */
    private function validateUserType(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet($this->csvTransactionDataFormatter::COLUMN_USER_TYPE_NUMBER)
            ->validateInArrayField(
                $this->validatedData[$this->csvTransactionDataFormatter::COLUMN_USER_TYPE_NUMBER],
                ['private', 'business']
            );
    }

    /**
     * Validate type column.
     *
     * @return $this
     * @throws RequiredFieldNotSet|IncorrectFieldFormat
     */
    private function validateType(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet($this->csvTransactionDataFormatter::COLUMN_TYPE_NUMBER)
            ->validateInArrayField(
                $this->validatedData[$this->csvTransactionDataFormatter::COLUMN_TYPE_NUMBER],
                ['deposit', 'withdraw']
            );
    }

    /**
     * Validate amount column.
     *
     * @return $this
     * @throws RequiredFieldNotSet|IncorrectFieldFormat
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
     * @throws RequiredFieldNotSet|IncorrectFieldFormat
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
     * @throws RequiredFieldNotSet
     */
    private function validateColumnSet(int $columnNumber): CsvTransactionDataValidator
    {
        if (empty($this->validatedData[$columnNumber])) {
            throw new RequiredFieldNotSet('Required column is not set');
        }

        return $this;
    }
}
