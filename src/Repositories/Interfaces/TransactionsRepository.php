<?php

declare(strict_types=1);

namespace CommissionTask\Repositories\Interfaces;

use CommissionTask\Entities\Transaction;
use DateTime;

interface TransactionsRepository
{
    /**
     * Get all transactions from repository.
     *
     * @return Transaction[]
     */
    public function all(): array;

    /**
     * Get filtered transactions applying given filter method.
     *
     * @return Transaction[]
     */
    public function filter(callable $filterMethod): array;

    /**
     * Get transaction from repository by given ID.
     */
    public function read(int $id): Transaction;

    /**
     * Save transaction to repository.
     */
    public function create(Transaction $transaction): void;

    /**
     * Delete transaction by given ID.
     */
    public function delete(int $id): void;

    /**
     * Delete all transactions.
     */
    public function deleteAll(): void;

    /**
     * Get withdraw transactions of user from one date to another date.
     *
     * @return Transaction[]
     */
    public function getUserWithdrawTransactionsBetweenDates(int $userId, DateTime $fromDate, DateTime $toDate): array;
}
