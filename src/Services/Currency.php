<?php

declare(strict_types=1);

namespace CommissionTask\Services;

class Currency
{
    const BASE_CURRENCY_CODE = 'EUR';

    /**
     * Convert amount to given currency by given currency code.
     */
    public function convertAmountToCurrency(string $amount, string $fromCurrencyCode, string $toCurrencyCode): string
    {
        return '';
    }
}
