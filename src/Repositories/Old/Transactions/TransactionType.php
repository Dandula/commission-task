<?php

declare(strict_types=1);

namespace CommissionTask\Repositories\Transactions;

use CommissionTask\Exceptions\CommissionTaskArgumentException;

class TransactionType
{
    const TYPE_DEPOSIT_ID = 1;
    const TYPE_WITHDRAW_ID = 2;

    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';

    private static $validTypes = [
        self::TYPE_DEPOSIT_ID => self::TYPE_DEPOSIT,
        self::TYPE_WITHDRAW_ID => self::TYPE_WITHDRAW,
    ];

    /**
     * @var int $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    public static function fromInt(int $typeId)
    {
        self::ensureIsValidId($typeId);

        return new self($typeId, self::$validTypes[$typeId]);
    }

    public static function fromString(string $typeName)
    {
        self::ensureIsValidName($typeName);
        $typeId = array_search($typeName, self::$validTypes);

        if ($typeId === false) {
            throw new CommissionTaskArgumentException('Invalid type given!');
        }

        return new self($typeId, $typeName);
    }

    private function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function toInt(): int
    {
        return $this->id;
    }

    public function toString(): string
    {
        return $this->name;
    }

    private static function ensureIsValidId(int $type)
    {
        if (!in_array($type, array_keys(self::$validTypes), true)) {
            throw new CommissionTaskArgumentException('Invalid type ID given');
        }
    }

    private static function ensureIsValidName(string $type)
    {
        if (!in_array($type, self::$validTypes, true)) {
            throw new CommissionTaskArgumentException('Invalid type name given');
        }
    }
}
