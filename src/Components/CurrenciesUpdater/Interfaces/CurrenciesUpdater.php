<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesUpdater\Interfaces;

use CommissionTask\Components\CurrenciesUpdater\Exceptions\CurrenciesUpdaterException;

interface CurrenciesUpdater
{
    /**
     * Update currencies to currencies repository.
     *
     * @throws CurrenciesUpdaterException
     */
    public function updateCurrencies(mixed $currenciesData): void;
}
