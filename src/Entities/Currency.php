<?php

declare(strict_types=1);

namespace CommissionTask\Entities;

use DateTime;

class Currency extends BaseEntity
{
    /**
     * Create currency entity instance.
     */
    public function __construct(
        private string $currencyCode,
        private bool $isBase,
        private float $rate,
        private DateTime $rateUpdatedAt
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
     * Is base currency flag setter.
     */
    public function setIsBase(bool $isBase): void
    {
        $this->isBase = $isBase;
    }

    /**
     * Is base currency flag getter.
     */
    public function getIsBase(): bool
    {
        return $this->isBase;
    }

    /**
     * Rate setter.
     */
    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }

    /**
     * Rate getter.
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * Rate updated at setter.
     */
    public function setRateUpdatedAt(DateTime $rateUpdatedAt): void
    {
        $this->rateUpdatedAt = $rateUpdatedAt;
    }

    /**
     * Rate updated at getter.
     */
    public function getRateUpdatedAt(): DateTime
    {
        return $this->rateUpdatedAt;
    }
}
