<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionsDataReaders\Interfaces;

use CommissionTask\Components\TransactionsDataReaders\Exceptions\Interfaces\TransactionsDataReaderException;

interface TransactionsDataReader
{
    /**
     * Read transactions data.
     *
     * @throws TransactionsDataReaderException
     */
    public function readTransactionsData(): array;
}
