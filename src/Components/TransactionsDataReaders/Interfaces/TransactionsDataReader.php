<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionsDataReaders\Interfaces;

interface TransactionsDataReader
{
    /**
     * Read transactions data.
     *
     * @return array
     */
    public function readTransactionsData(): array;
}