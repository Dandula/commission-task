<?php

declare(strict_types=1);

namespace CommissionTask\Components\Storage\Interfaces;

use CommissionTask\Components\Storage\Exceptions\CommissionTaskOutOfBoundsStorageException;

interface Storage
{
    /**
     * Find all data items at storage.
     *
     * @return mixed
     */
    public function findAll(string $part);

    /**
     * Find data item at storage by given ID.
     *
     * @param int|string $id
     * @return mixed
     * @throws CommissionTaskOutOfBoundsStorageException
     */
    public function findById(string $part, $id);

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
     * @throws CommissionTaskOutOfBoundsStorageException
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
