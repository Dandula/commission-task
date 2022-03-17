<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidator\Interfaces;

use CommissionTask\Components\CurrenciesDataValidator\Exceptions\CurrenciesDataValidatorException;

interface CurrenciesDataValidator
{
    public const CURRENCY_CODE_REGEXP = '/^[A-Z]{3}$/';

    /**
     * Read transactions data.
     *
     * @throws CurrenciesDataValidatorException
     */
    public function validateCurrenciesData(array $currenciesData): void;
}
