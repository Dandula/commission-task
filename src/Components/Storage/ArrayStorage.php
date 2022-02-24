<?php

declare(strict_types=1);

namespace CommissionTask\Components\Storage;

use CommissionTask\Components\Storage\Exceptions\CommissionTaskOutOfBoundsStorageException;
use CommissionTask\Components\Storage\Interfaces\Storage as StorageContract;

class ArrayStorage implements StorageContract
{
    private $array = [];

    /**
     * @inheritDoc
     */
    public function findAll(string $part)
    {
        return $this->array[$part];
    }

    /**
     * @inheritDoc
     */
    public function findById(string $part, $id)
    {
        return $this->array[$part][$id];
    }

    /**
     * @inheritDoc
     */
    public function create(string $part, $data)
    {
        return $this->array[$part][] = $data;
    }

    /**
     * @inheritDoc
     */
    public function update(string $part, $id, $data)
    {
        if (!isset($this->array[$part][$id])) {
            throw new CommissionTaskOutOfBoundsStorageException(
                "The data item with the ID $id at part '$part' does not exist"
            );
        }

        return $this->array[$part][$id] = $data;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $part, $id)
    {
        unset($this->array[$part][$id]);
    }
}
