<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidator;

use CommissionTask\Components\DataFormatter\CsvTransactionDataFormatter;
use CommissionTask\Components\TransactionDataValidator\Exceptions\TransactionDataValidatorException;
use CommissionTask\Components\TransactionDataValidator\Interfaces\TransactionDataValidator as TransactionDataValidatorContract;
use CommissionTask\Components\TransactionDataValidator\Traits\FieldFormat;
use CommissionTask\Entities\Transaction;
use CommissionTask\Services\Currency as CurrencyService;
use CommissionTask\Services\Date as DateService;

class CsvTransactionDataValidator implements TransactionDataValidatorContract
{
    use FieldFormat;

    private array $validatedData;

    /**
     * Create CSV transaction data validator instance.
     */
    public function __construct(
        private CsvTransactionDataFormatter $csvTransactionDataFormatter,
        private DateService $dateService,
        private CurrencyService $currencyService
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function validateTransactionData(array $transactionData): void
    {
        $this->validatedData = $transactionData;

        $this->validateDate()
            ->validateUserId()
            ->validateUserType()
            ->validateType()
            ->validateAmount()
            ->validateCurrencyCode();
    }

    /**
     * Validate date column.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateDate(): self
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
    private function validateUserId(): self
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
    private function validateUserType(): self
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
    private function validateType(): self
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
    private function validateAmount(): self
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
    private function validateCurrencyCode(): self
    {
        return $this->validateColumnSet($this->csvTransactionDataFormatter::COLUMN_CURRENCY_CODE_NUMBER)
            ->validateInArrayField(
                $this->validatedData[$this->csvTransactionDataFormatter::COLUMN_CURRENCY_CODE_NUMBER],
                $this->currencyService->getAcceptableCurrenciesCodes()
            );
    }

    /**
     * Validate column is set.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateColumnSet(int $columnNumber): self
    {
        if (empty($this->validatedData[$columnNumber])) {
            throw new TransactionDataValidatorException(TransactionDataValidatorException::REQUIRED_FIELD_NOT_SET_MESSAGE);
        }

        return $this;
    }
}
