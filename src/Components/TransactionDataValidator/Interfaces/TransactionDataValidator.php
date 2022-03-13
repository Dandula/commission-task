<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidator\Interfaces;

use CommissionTask\Components\TransactionDataValidator\Exceptions\Interfaces\TransactionDataValidatorException;

interface TransactionDataValidator
{
    const DEFAULT_DATE_FORMAT = 'Y-m-d';

    const UNSIGNED_INTEGER_REGEXP = '/^[1-9]\d*$/';
    const UNSIGNED_FLOAT_REGEXP = '/^[1-9]\d*\.?\d*$/';
    const CURRENCY_CODE_REGEXP = '/^[A-Z]{3}$/';

    /**
     * Read transactions data.
     *
     * @return void
     *
     * @throws TransactionDataValidatorException
     */
    public function validateTransactionData(array $transactionData);
}
