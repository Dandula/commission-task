<?php

declare(strict_types=1);

namespace CommissionTask\Repositories;

use CommissionTask\Components\Storage\Interfaces\Storage;
use CommissionTask\Entities\Transaction;
use CommissionTask\Repositories\Interfaces\TransactionsRepository as TransactionsRepositoryContract;
use DateTime;

class TransactionsRepository implements TransactionsRepositoryContract
{
    private const REPOSITORY_PART = 'transactions';

    /**
     * Create transactions repository instance.
     */
    public function __construct(private Storage $storage)
    {
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
    public function create(Transaction $transaction): void
    {
        $this->storage->create(self::REPOSITORY_PART, $transaction);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): void
    {
        $this->storage->delete(self::REPOSITORY_PART, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAll(): void
    {
        $this->storage->deleteAll(self::REPOSITORY_PART);
    }

    /**
     * {@inheritDoc}
     */
    public function getEarlyUserWithdrawTransactionsFromStartOfWeek(int $userId, DateTime $fromDate, int $toId): array
    {
        $filteredTransactions = [];

        foreach ($this->all() as $id => $transaction) {
            if (
                $id < $toId
                && $transaction->getUserId() === $userId
                && $transaction->getType() === Transaction::TYPE_WITHDRAW
                && $transaction->getDate() >= $fromDate
            ) {
                $filteredTransactions[$id] = $transaction;
            }
        }

        return $filteredTransactions;
    }
}
