<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataReaders\Interfaces;

use CommissionTask\Components\CurrenciesDataReaders\Exceptions\Interfaces\CurrenciesDataReaderException;

interface CurrenciesDataReader
{
    /**
     * Read currencies data.
     *
     * @throws CurrenciesDataReaderException
     */
    public function readCurrenciesData(): array;
}
