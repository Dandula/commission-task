<?php

declare(strict_types=1);

namespace CommissionTask\Components\Storage\Interfaces;

use CommissionTask\Components\Storage\Exceptions\OutOfBoundsStorageException;

interface Storage
{
    /**
     * Find all data items at storage.
     */
    public function findAll(string $part): array;

    /**
     * Find data item at storage by given ID.
     *
     * @throws OutOfBoundsStorageException
     */
    public function findById(string $part, int|string $id): mixed;

    /**
     * Get filtered data items applying given filter method.
     */
    public function filter(string $part, callable $filterMethod): array;

    /**
     * Create data item at storage.
     */
    public function create(string $part, mixed $data): void;

    /**
     * Update data item at storage by given ID.
     *
     * @throws OutOfBoundsStorageException
     */
    public function update(string $part, int|string $id, mixed $data): void;

    /**
     * Delete data item from storage by given ID.
     */
    public function delete(string $part, int|string $id): void;

    /**
     * Delete all data items from storage.
     */
    public function deleteAll(string $part): void;
}
