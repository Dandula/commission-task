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
    public function getEarlyUserWithdrawTransactionsFromDate(Transaction $transaction, DateTime $fromDate): array
    {
        $toId = $transaction->getId();
        $userId = $transaction->getUserId();

        $filteredTransactions = [];

        foreach ($this->all() as $checkedTransactionId => $checkedTransaction) {
            if (
                $checkedTransactionId < $toId
                && $checkedTransaction->getDate() >= $fromDate
                && $checkedTransaction->getUserId() === $userId
                && $checkedTransaction->getType() === Transaction::TYPE_WITHDRAW
            ) {
                $filteredTransactions[$checkedTransactionId] = $checkedTransaction;
            }
        }

        return $filteredTransactions;
    }
}
