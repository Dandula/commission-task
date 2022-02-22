<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionsDataReaders\Interfaces;

use CommissionTask\Exceptions\CommissionTaskException;

interface TransactionsDataReader
{
    /**
     * Read transactions data.
     *
     * @return array
     */
    public function readTransactionsData(): array;

    /**
     * Read transactions raw data.
     *
     * @return array
     * @throws CommissionTaskException
     */
    public function readTransactionsRawData(): array;

    /**
     * Prepare transactions data.
     *
     * @param array $transactionsRawData
     * @return array
     */
    public function prepareTransactionsData(array $transactionsRawData): array;
}