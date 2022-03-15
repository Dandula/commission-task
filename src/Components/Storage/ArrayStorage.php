<?php

declare(strict_types=1);

namespace CommissionTask\Components\Storage;

use CommissionTask\Components\Storage\Exceptions\OutOfBoundsStorageException;
use CommissionTask\Components\Storage\Interfaces\Storage as StorageContract;

class ArrayStorage implements StorageContract
{
    /**
     * @var array[]
     */
    private array $array = [];

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
    public function findById(string $part, int|string $id): mixed
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
        return array_filter($this->safeAccessToPart($part), $filterMethod);
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $part, mixed $data): void
    {
        $this->array[$part][] = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $part, int|string $id, mixed $data): void
    {
        if (!isset($this->array[$part][$id])) {
            throw new OutOfBoundsStorageException(sprintf(OutOfBoundsStorageException::DATA_ITEM_ID_DOESNT_EXISTS_MESSAGE, (string) $id, $part));
        }

        $this->array[$part][$id] = $data;
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
}
