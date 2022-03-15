<?php

declare(strict_types=1);

namespace CommissionTask\Factories;

use CommissionTask\Components\DataFormatter\CurrenciesUpdaterDataFormatter;
use CommissionTask\Entities\Currency;
use CommissionTask\Factories\Interfaces\CurrencyFactory as CurrencyFactoryContract;

class ApiCurrencyFactory implements CurrencyFactoryContract
{
    /**
     * Create a new API currency factory instance.
     */
    public function __construct(
        private CurrenciesUpdaterDataFormatter $currenciesUpdaterDataFormatter
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function makeCurrency(mixed $currencyData): Currency
    {
        $currency = new Currency();

        $currency->setCurrencyCode($currencyData[$this->currenciesUpdaterDataFormatter::CURRENCIES_RATES_CURRENCY_CODE]);
        $currency->setIsBase($currencyData[$this->currenciesUpdaterDataFormatter::CURRENCIES_RATES_IS_BASE]);
        $currency->setRate($currencyData[$this->currenciesUpdaterDataFormatter::CURRENCIES_RATES_RATE]);
        $currency->setRateUpdatedAt($currencyData[$this->currenciesUpdaterDataFormatter::CURRENCIES_RATES_RATE_UPDATED_AT]);

        return $currency;
    }
}
