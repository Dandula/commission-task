<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionsDataReaders\Traits;

trait TransactionsReadProcess
{
    /**
     * @inheritDoc
     */
    public function readTransactionsData(): array
    {
        $transactionsRawData = $this->readTransactionsRawData();

        return $this->prepareTransactionsData($transactionsRawData);
    }
}