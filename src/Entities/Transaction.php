<?php

declare(strict_types=1);

namespace CommissionTask\Entities;

use DateTime;

class Transaction
{
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';

    const USER_TYPE_PRIVATE = 'private';
    const USER_TYPE_BUSINESS = 'business';

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $amount;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * Create transaction entity instance.
     *
     * @return void
     */
    public function __construct() { }

    /**
     * Date setter.
     *
     * @return void
     */
    public function setDate(DateTime $date)
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
     *
     * @return void
     */
    public function setUserId(int $userId)
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
     *
     * @return void
     */
    public function setUserType(string $userType)
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
     *
     * @return void
     */
    public function setType(string $type)
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
     *
     * @return void
     */
    public function setAmount(string $amount)
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
}
