<?php

declare(strict_types=1);

namespace CommissionTask\Entities;

class Currency extends BaseEntity
{
    /**
     * Create currency entity instance.
     */
    public function __construct(
        private string $currencyCode,
        private string $rate
    ) {
    }

    /**
     * Currency code setter.
     */
    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * Currency code getter.
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * Rate setter.
     */
    public function setRate(string $rate): void
    {
        $this->rate = $rate;
    }

    /**
     * Rate getter.
     */
    public function getRate(): string
    {
        return $this->rate;
    }
}
