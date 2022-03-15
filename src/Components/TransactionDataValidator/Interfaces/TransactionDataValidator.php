<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidator\Interfaces;

use CommissionTask\Components\TransactionDataValidator\Exceptions\TransactionDataValidatorException;

interface TransactionDataValidator
{
    public const DEFAULT_DATE_FORMAT = 'Y-m-d';

    public const UNSIGNED_INTEGER_REGEXP = '/^[1-9]\d*$/';
    public const UNSIGNED_FLOAT_REGEXP = '/^[1-9]\d*\.?\d*$/';
    public const CURRENCY_CODE_REGEXP = '/^[A-Z]{3}$/';

    /**
     * Read transactions data.
     *
     * @throws TransactionDataValidatorException
     */
    public function validateTransactionData(array $transactionData): void;
}
