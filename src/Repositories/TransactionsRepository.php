<?php

declare(strict_types=1);

namespace CommissionTask\Repositories;

use CommissionTask\Components\Storage\Interfaces\Storage;
use CommissionTask\Entities\Transaction;
use CommissionTask\Repositories\Interfaces\TransactionsRepository as TransactionsRepositoryContract;

class TransactionsRepository implements TransactionsRepositoryContract
{
    const TRANSACTIONS_PART = 'transactions';

    /**
     * @var Storage
     */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->storage->findAll(self::TRANSACTIONS_PART);
    }

    /**
     * @inheritDoc
     */
    public function read(int $id): Transaction
    {
        return $this->storage->findById(self::TRANSACTIONS_PART, $id);
    }

    /**
     * @inheritDoc
     */
    public function create(Transaction $transaction)
    {
        $this->storage->create(self::TRANSACTIONS_PART, $transaction);
    }

    /**
     * @inheritDoc
     */
    public function update(int $id, Transaction $transaction)
    {
        $this->storage->update(self::TRANSACTIONS_PART, $id, $transaction);
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id)
    {
        $this->storage->delete(self::TRANSACTIONS_PART, $id);
    }
}
