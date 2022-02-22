<?php

declare(strict_types=1);

namespace CommissionTask\Repositories\Transactions;

use CommissionTask\Exceptions\CommissionTaskArgumentException;

class TransactionId
{
    /**
     * @var int $id
     */
    private $id;

    public static function fromInt(int $id): TransactionId
    {
        self::ensureIsValid($id);

        return new self($id);
    }

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public function toInt(): int
    {
        return $this->id;
    }

    private static function ensureIsValid(int $id)
    {
        if ($id <= 0) {
            throw new CommissionTaskArgumentException('Invalid TransactionId given');
        }
    }
}
