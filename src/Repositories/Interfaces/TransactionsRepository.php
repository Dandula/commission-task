<?php

declare(strict_types=1);

namespace CommissionTask\Repositories\Interfaces;

use CommissionTask\Entities\Transaction;

interface TransactionsRepository
{
    /**
     * Get all transactions from repository.
     *
     * @return Transaction[]
     */
    public function all(): array;

    /**
     * Get transaction from repository by given ID.
     */
    public function read(int $id): Transaction;

    /**
     * Save transaction to repository.
     *
     * @return void
     */
    public function create(Transaction $transaction);

    /**
     * Update transaction by given ID.
     *
     * @return void
     */
    public function update(int $id, Transaction $transaction);

    /**
     * Update transaction by given ID.
     *
     * @return void
     */
    public function delete(int $id);
}
