<?php

declare(strict_types=1);

namespace CommissionTask\Components\Storage;

use CommissionTask\Components\Storage\Exceptions\OutOfBoundsStorageException;
use CommissionTask\Components\Storage\Interfaces\Storage as StorageContract;
use CommissionTask\Entities\BaseEntity as Entity;

class ArrayStorage implements StorageContract
{
    public const MIN_ID = 0;

    /**
     * @var array[]
     */
    private array $array = [];

    /**
     * @var int[]
     */
    private array $lastIds = [];

    /**
     * {@inheritDoc}
     */
    public function findAll(string $part): array
    {
        return $this->safeAccessToPart($part);
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $part, int|string $id): Entity
    {
        if (!isset($this->array[$part][$id])) {
            throw new OutOfBoundsStorageException(sprintf(OutOfBoundsStorageException::DATA_ITEM_ID_DOESNT_EXISTS_MESSAGE, (string) $id, $part));
        }

        return $this->array[$part][$id];
    }

    /**
     * {@inheritDoc}
     */
    public function filter(string $part, callable $filterMethod): array
    {
        return array_filter($this->safeAccessToPart($part), $filterMethod, mode: ARRAY_FILTER_USE_BOTH);
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $part, Entity $entityInstance): void
    {
        $generatedId = $this->generateId($part);

        $entityInstance->setId($generatedId);

        $this->array[$part][$generatedId] = $entityInstance;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $part, int|string $id, Entity $entityInstance): void
    {
        $updatingInstance = clone $entityInstance;
        $updatingInstance->setId($id);

        if (!isset($this->array[$part][$id])) {
            throw new OutOfBoundsStorageException(sprintf(OutOfBoundsStorageException::DATA_ITEM_ID_DOESNT_EXISTS_MESSAGE, (string) $id, $part));
        }

        $this->array[$part][$id] = $entityInstance;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $part, int|string $id): void
    {
        unset($this->array[$part][$id]);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAll(string $part): void
    {
        $this->array[$part] = [];
    }

    /**
     * Safe access to part of repository.
     */
    private function safeAccessToPart(string $part): array
    {
        return $this->array[$part] ?? [];
    }

    /**
     * Generate autoincrement ID for next data item.
     */
    private function generateId(string $part): int
    {
        $this->lastIds[$part] = isset($this->lastIds[$part]) ? $this->lastIds[$part] + 1 : self::MIN_ID;

        return $this->lastIds[$part];
    }
}
