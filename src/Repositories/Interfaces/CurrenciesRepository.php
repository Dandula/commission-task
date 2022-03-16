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
     */
    public function create(Currency $currency): void;

    /**
     * Update currency by given ID.
     */
    public function update(int $id, Currency $currency): void;

    /**
     * Delete currency by given ID.
     */
    public function delete(int $id): void;

    /**
     * Delete all currencies.
     */
    public function deleteAll(): void;

    /**
     * Get currency by currency code.
     */
    public function getCurrencyByCode(string $currencyCode): ?Currency;
}
