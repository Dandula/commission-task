<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidator\Interfaces;

use CommissionTask\Components\CurrenciesDataValidator\Exceptions\Interfaces\CurrenciesDataValidatorException;

interface CurrenciesDataValidator
{
    const DEFAULT_DATE_FORMAT = 'Y-m-d';

    const CURRENCY_CODE_REGEXP = '/^[A-Z]{3}$/';

    /**
     * Read transactions data.
     *
     * @return void
     *
     * @throws CurrenciesDataValidatorException
     */
    public function validateCurrenciesData(array $currenciesData);
}
