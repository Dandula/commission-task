<?php

declare(strict_types=1);

namespace CommissionTask\Factories\Interfaces;

use CommissionTask\Entities\Currency;

interface CurrencyFactory
{
    /**
     * Make currency entity.
     *
     * @param mixed $currencyData
     */
    public function makeCurrency($currencyData): Currency;
}
