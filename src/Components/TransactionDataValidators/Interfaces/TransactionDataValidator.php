<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidators\Interfaces;

use CommissionTask\Components\TransactionDataValidators\Exceptions\Interfaces\TransactionDataValidatorException;

interface TransactionDataValidator
{
    const DEFAULT_DATE_FORMAT = 'Y-m-d';

    /**
     * Read transactions data.
     *
     * @return void
     * @throws TransactionDataValidatorException
     */
    public function validateTransactionData(array $transactionData);
}
