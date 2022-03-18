<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionsDataReader\Interfaces;

use CommissionTask\Components\TransactionsDataReader\Exceptions\TransactionsDataReaderException;

interface TransactionsDataReader
{
    /**
     * Read transactions data.
     *
     * @throws TransactionsDataReaderException
     */
    public function readTransactionsData(): iterable;
}
