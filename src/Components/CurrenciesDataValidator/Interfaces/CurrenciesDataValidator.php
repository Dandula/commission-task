<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidator\Interfaces;

use CommissionTask\Components\CurrenciesDataValidator\Exceptions\Interfaces\CurrenciesDataValidatorException;

interface CurrenciesDataValidator
{
    public const DEFAULT_DATE_FORMAT = 'Y-m-d';

    public const CURRENCY_CODE_REGEXP = '/^[A-Z]{3}$/';

    /**
     * Read transactions data.
     *
     * @throws CurrenciesDataValidatorException
     */
    public function validateCurrenciesData(array $currenciesData): void;
}
