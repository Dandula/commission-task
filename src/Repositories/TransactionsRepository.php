<?php

declare(strict_types=1);

namespace CommissionTask\Repositories;

use CommissionTask\Components\Storage\Interfaces\Storage;
use CommissionTask\Entities\Transaction;
use CommissionTask\Repositories\Interfaces\TransactionsRepository as TransactionsRepositoryContract;

class TransactionsRepository implements TransactionsRepositoryContract
{
    const REPOSITORY_PART = 'transactions';

    /**
     * @var Storage
     */
    private $storage;

    /**
     * Create transactions repository instance.
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritDoc}
     */
    public function all(): array
    {
        return $this->storage->findAll(self::REPOSITORY_PART);
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $id): Transaction
    {
        return $this->storage->findById(self::REPOSITORY_PART, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function filter(callable $filterMethod): array
    {
        return $this->storage->filter(self::REPOSITORY_PART, $filterMethod);
    }

    /**
     * {@inheritDoc}
     */
    public function create(Transaction $transaction)
    {
        $this->storage->create(self::REPOSITORY_PART, $transaction);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id)
    {
        $this->storage->delete(self::REPOSITORY_PART, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAll()
    {
        $this->storage->deleteAll(self::REPOSITORY_PART);
    }
}
