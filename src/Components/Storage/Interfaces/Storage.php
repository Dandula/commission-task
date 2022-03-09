<?php

declare(strict_types=1);

namespace CommissionTask\Components\Storage\Interfaces;

use CommissionTask\Components\Storage\Exceptions\Interfaces\StorageException;
use CommissionTask\Components\Storage\Exceptions\UndefinedPartStorageException;

interface Storage
{
    /**
     * Find all data items at storage.
     *
     * @return mixed
     * @throws UndefinedPartStorageException
     */
    public function findAll(string $part);

    /**
     * Find data item at storage by given ID.
     *
     * @param int|string $id
     * @return mixed
     * @throws UndefinedPartStorageException|StorageException
     */
    public function findById(string $part, $id);

    /**
     * Get filtered data items applying given filter method.
     *
     * @return mixed
     * @throws UndefinedPartStorageException
     */
    public function filter(string $part, callable $filterMethod);

    /**
     * Create data item at storage.
     *
     * @param mixed $data
     * @return void
     */
    public function create(string $part, $data);

    /**
     * Update data item at storage by given ID.
     *
     * @param int|string $id
     * @param mixed $data
     * @return void
     * @throws StorageException
     */
    public function update(string $part, $id, $data);

    /**
     * Delete data item from storage by given ID.
     *
     * @param int|string $id
     * @return void
     */
    public function delete(string $part, $id);
}
