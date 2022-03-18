<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidator;

use CommissionTask\Components\TransactionDataValidator\Exceptions\TransactionDataValidatorException;
use CommissionTask\Components\TransactionDataValidator\Interfaces\TransactionDataValidator as TransactionDataValidatorContract;
use CommissionTask\Components\TransactionDataValidator\Traits\FieldFormat;
use CommissionTask\Entities\Transaction;
use CommissionTask\Services\Config as ConfigService;
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
        private ConfigService $configService,
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
        $columnDateNumber = $this->configService->getTransactionsCsvColumnNumber('date');

        return $this->validateColumnSet($columnDateNumber)
            ->validateDateField(
                $this->validatedData[$columnDateNumber],
                $this->configService->getConfigByName('transactionsCsv.dateFormat')
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
        $columnUserIdNumber = $this->configService->getTransactionsCsvColumnNumber('userId');

        return $this->validateColumnSet($columnUserIdNumber)
            ->validateUnsignedIntegerField($this->validatedData[$columnUserIdNumber]);
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
        $columnUserTypeNumber = $this->configService->getTransactionsCsvColumnNumber('userType');

        return $this->validateColumnSet($columnUserTypeNumber)
            ->validateInArrayField(
                $this->validatedData[$columnUserTypeNumber],
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
        $columnTypeNumber = $this->configService->getTransactionsCsvColumnNumber('type');

        return $this->validateColumnSet($columnTypeNumber)
            ->validateInArrayField(
                $this->validatedData[$columnTypeNumber],
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
        $columnAmountNumber = $this->configService->getTransactionsCsvColumnNumber('amount');

        return $this->validateColumnSet($columnAmountNumber)
            ->validateUnsignedFloatField($this->validatedData[$columnAmountNumber]);
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
        $columnCurrencyCodeNumber = $this->configService->getTransactionsCsvColumnNumber('currencyCode');

        return $this->validateColumnSet($columnCurrencyCodeNumber)
            ->validateInArrayField(
                $this->validatedData[$columnCurrencyCodeNumber],
                $this->configService->getAcceptableCurrenciesCodes()
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
