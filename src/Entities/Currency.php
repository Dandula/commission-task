<?php

declare(strict_types=1);

namespace CommissionTask\Entities;

use DateTime;

class Currency
{
    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var bool
     */
    private $isBase;

    /**
     * @var float
     */
    private $rate;

    /**
     * @var DateTime
     */
    private $rateUpdatedAt;

    /**
     * Currency code setter.
     *
     * @return void
     */
    public function setCurrencyCode(string $currencyCode)
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
     *
     * @return void
     */
    public function setIsBase(bool $isBase)
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
     *
     * @return void
     */
    public function setRate(float $rate)
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
     *
     * @return void
     */
    public function setRateUpdatedAt(DateTime $rateUpdatedAt)
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
