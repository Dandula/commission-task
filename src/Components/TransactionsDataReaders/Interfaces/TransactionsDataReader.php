<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionsDataReaders\Interfaces;

use CommissionTask\Components\TransactionsDataReaders\Exceptions\Interfaces\CommissionTaskReaderException;

interface TransactionsDataReader
{
    /**
     * Read transactions data.
     *
     * @throws CommissionTaskReaderException
     */
    public function readTransactionsData(): array;
}
