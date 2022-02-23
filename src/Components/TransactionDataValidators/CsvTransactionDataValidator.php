<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidators;

use CommissionTask\Components\TransactionDataValidators\Exceptions\IncorrectFieldFormat;
use CommissionTask\Components\TransactionDataValidators\Exceptions\IncorrectFieldsNumber;
use CommissionTask\Components\TransactionDataValidators\Exceptions\RequiredFieldNotSet;
use CommissionTask\Components\TransactionDataValidators\Interfaces\TransactionDataValidator as TransactionDataValidatorContract;
use CommissionTask\Components\TransactionDataValidators\Traits\FieldFormat;
use CommissionTask\Services\Date;

class CsvTransactionDataValidator implements TransactionDataValidatorContract
{
    use FieldFormat;

    const COLUMNS_NUMBER = 6;

    const COLUMN_DATE_NUMBER = 0;
    const COLUMN_USER_ID_NUMBER = 1;
    const COLUMN_USER_TYPE_NUMBER = 2;
    const COLUMN_TYPE_NUMBER = 3;
    const COLUMN_AMOUNT_NUMBER = 4;
    const COLUMN_CURRENCY_CODE_NUMBER = 5;

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
    public function __construct(Date $dateService)
    {
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
        if (count($this->validatedData) !== self::COLUMNS_NUMBER) {
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
        return $this->validateColumnSet(self::COLUMN_DATE_NUMBER)
            ->validateDateYmdField($this->validatedData[self::COLUMN_DATE_NUMBER]);
    }

    /**
     * Validate user ID column.
     *
     * @return $this
     * @throws RequiredFieldNotSet|IncorrectFieldFormat
     */
    private function validateUserId(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet(self::COLUMN_USER_ID_NUMBER)
            ->validateUnsignedIntegerField($this->validatedData[self::COLUMN_USER_ID_NUMBER]);
    }

    /**
     * Validate user type column.
     *
     * @return $this
     * @throws RequiredFieldNotSet|IncorrectFieldFormat
     */
    private function validateUserType(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet(self::COLUMN_USER_TYPE_NUMBER)
            ->validateInArrayField($this->validatedData[self::COLUMN_USER_TYPE_NUMBER], ['private', 'business']);
    }

    /**
     * Validate type column.
     *
     * @return $this
     * @throws RequiredFieldNotSet|IncorrectFieldFormat
     */
    private function validateType(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet(self::COLUMN_TYPE_NUMBER)
            ->validateInArrayField($this->validatedData[self::COLUMN_TYPE_NUMBER], ['deposit', 'withdraw']);
    }

    /**
     * Validate amount column.
     *
     * @return $this
     * @throws RequiredFieldNotSet|IncorrectFieldFormat
     */
    private function validateAmount(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet(self::COLUMN_AMOUNT_NUMBER)
            ->validateUnsignedFloatField($this->validatedData[self::COLUMN_AMOUNT_NUMBER]);
    }

    /**
     * Validate currency code column.
     *
     * @return $this
     * @throws RequiredFieldNotSet|IncorrectFieldFormat
     */
    private function validateCurrencyCode(): CsvTransactionDataValidator
    {
        return $this->validateColumnSet(self::COLUMN_CURRENCY_CODE_NUMBER)
            ->validateCurrencyCodeField($this->validatedData[self::COLUMN_CURRENCY_CODE_NUMBER]);
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
