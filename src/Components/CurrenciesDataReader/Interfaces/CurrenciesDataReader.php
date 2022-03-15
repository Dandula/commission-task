<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataReader\Interfaces;

use CommissionTask\Components\CurrenciesDataReader\Exceptions\CurrenciesDataReaderException;

interface CurrenciesDataReader
{
    /**
     * Read currencies data.
     *
     * @throws CurrenciesDataReaderException
     */
    public function readCurrenciesData(): array;
}
