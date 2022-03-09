<?php

declare(strict_types=1);

namespace CommissionTask\Repositories\Interfaces;

use CommissionTask\Entities\Currency;

interface CurrenciesRepository
{
    /**
     * Get all currencies from repository.
     *
     * @return Currency[]
     */
    public function all(): array;

    /**
     * Get filtered currencies applying given filter method.
     *
     * @return Currency[]
     */
    public function filter(callable $filterMethod): array;

    /**
     * Get currency from repository by given ID.
     */
    public function read(int $id): Currency;

    /**
     * Save currency to repository.
     *
     * @return void
     */
    public function create(Currency $currency);

    /**
     * Update currency by given ID.
     *
     * @return void
     */
    public function update(int $id, Currency $currency);

    /**
     * Update currency by given ID.
     *
     * @return void
     */
    public function delete(int $id);
}
