<?php

declare(strict_types=1);

namespace CommissionTask\Entities;

use DateTime;

class Transaction
{
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAW = 'withdraw';

    public const USER_TYPE_PRIVATE = 'private';
    public const USER_TYPE_BUSINESS = 'business';

    private DateTime $date;
    private int $userId;
    private string $userType;
    private string $type;
    private string $amount;
    private string $currencyCode;

    /**
     * Date setter.
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * Date getter.
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * User ID setter.
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * User ID getter.
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * User type setter.
     */
    public function setUserType(string $userType): void
    {
        $this->userType = $userType;
    }

    /**
     * User type getter.
     */
    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * Type setter.
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Type getter.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Amount setter.
     */
    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * Amount getter.
     */
    public function getAmount(): string
    {
        return $this->amount;
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
}
