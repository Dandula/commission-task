<?php

declare(strict_types=1);

namespace CommissionTask\Repositories\Transactions;

use CommissionTask\Services\Date;
use DateTime;

class Transaction
{
    /**
     * @var TransactionId $id
     */
    public $id;

    /**
     * @var DateTime $date
     */
    public $date;

    /**
     * @var UserId $userId
     */
    public $userId;

    /**
     * @var UserType $userType
     */
    public $userType;

    /**
     * @var TransactionType $type
     */
    public $type;

    /**
     * @var string $amount
     */
    public $amount;

    /**
     * @var CurrencyCode $currencyCode
     */
    public $currencyCode;

    public static function withdraw(
        TransactionId $id,
        DateTime $date,
        UserId $userId,
        UserType $userType,
        string $amount,
        CurrencyCode $currencyCode
    ): Transaction
    {
        return new self(
            $id,
            $date,
            $userId,
            $userType,
            TransactionType::fromString(TransactionType::TYPE_WITHDRAW),
            $amount,
            $currencyCode
        );
    }

    public static function deposit(
        TransactionId $id,
        DateTime $date,
        UserId $userId,
        UserType $userType,
        string $amount,
        CurrencyCode $currencyCode
    ): Transaction
    {
        return new self(
            $id,
            $date,
            $userId,
            $userType,
            TransactionType::fromString(TransactionType::TYPE_DEPOSIT),
            $amount,
            $currencyCode
        );
    }

    public static function fromState(array $state): Transaction
    {
        $dateService = Date::getInstance();

        return new self(
            TransactionId::fromInt($state['id']),
            $dateService->parseYmd($state['date']),
            UserId::fromInt($state['userId']),
            UserType::fromInt($state['userType']),
            TransactionType::fromString($state['transactionType']),
            $state['amount'],
            CurrencyCode::fromString($state['currencyCode'])
        );
    }

    public function __construct(
        TransactionId $id,
        DateTime $date,
        UserId $userId,
        UserType $userType,
        TransactionType $type,
        string $amount,
        CurrencyCode $currencyCode
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->type = $type;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
    }

    public function getId(): TransactionId
    {
        return $this->id;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getUserType(): UserType
    {
        return $this->userType;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrencyCode(): CurrencyCode
    {
        return $this->currencyCode;
    }
}
