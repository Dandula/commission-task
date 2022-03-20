<?php

declare(strict_types=1);

namespace CommissionTask\Components\Storage\Interfaces;

use CommissionTask\Components\Storage\Exceptions\OutOfBoundsStorageException;
use CommissionTask\Entities\BaseEntity as Entity;

interface Storage
{
    /**
     * Find all instances at storage.
     *
     * @return Entity[]
     */
    public function findAll(string $part): array;

    /**
     * Find entity instance at storage by given ID.
     *
     * @throws OutOfBoundsStorageException
     */
    public function findById(string $part, int|string $id): Entity;

    /**
     * Get filtered entity instances applying given filter method.
     *
     * @return Entity[]
     */
    public function filter(string $part, callable $filterMethod): array;

    /**
     * Create entity instance at storage.
     */
    public function create(string $part, Entity $entityInstance): void;

    /**
     * Update entity instance at storage by given ID.
     *
     * @throws OutOfBoundsStorageException
     */
    public function update(string $part, int|string $id, Entity $entityInstance): void;

    /**
     * Delete entity instance from storage by given ID.
     */
    public function delete(string $part, int|string $id): void;

    /**
     * Delete all entity instances from storage.
     */
    public function deleteAll(string $part): void;
}
