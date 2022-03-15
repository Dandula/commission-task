<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesUpdater\Interfaces;

use CommissionTask\Components\CurrenciesUpdater\Exceptions\CurrenciesUpdaterException;

interface CurrenciesUpdater
{
    public const BASE_CURRENCY_RATE = 1;

    /**
     * Update currencies to currencies repository.
     *
     * @throws CurrenciesUpdaterException
     */
    public function updateCurrencies(mixed $currenciesData): void;
}
