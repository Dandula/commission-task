<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidators\Interfaces;

use CommissionTask\Components\TransactionDataValidators\Exceptions\Interfaces\CommissionTaskValidationException;

interface TransactionDataValidator
{
    /**
     * Read transactions data.
     *
     * @return void
     * @throws CommissionTaskValidationException
     */
    public function validateTransactionData(array $transactionData);
}
