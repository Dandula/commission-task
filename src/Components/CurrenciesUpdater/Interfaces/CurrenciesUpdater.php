<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesUpdater\Interfaces;

use CommissionTask\Components\CurrenciesUpdater\Exceptions\Interfaces\CurrenciesUpdaterException;

interface CurrenciesUpdater
{
    // TODO: move to config
    const BASE_CURRENCY_CODE = 'EUR';
    const BASE_CURRENCY_RATE = 1;

    /**
     * Update currencies to currencies repository.
     *
     * @param mixed $currenciesData
     * @return void
     * @throws CurrenciesUpdaterException
     */
    public function updateCurrencies($currenciesData);
}
