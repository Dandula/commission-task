<?php

declare(strict_types=1);

namespace CommissionTask\Components\Storage;

use CommissionTask\Components\Storage\Exceptions\OutOfBoundsStorageException;
use CommissionTask\Components\Storage\Interfaces\Storage as StorageContract;

class ArrayStorage implements StorageContract
{
    /**
     * @var array
     */
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
        if (!isset($this->array[$part][$id])) {
            throw new OutOfBoundsStorageException(
                sprintf(OutOfBoundsStorageException::DATA_ITEM_ID_DOESNT_EXISTS_MESSAGE, (string)$id, $part)
            );
        }

        return $this->array[$part][$id];
    }

    /**
     * @inheritDoc
     */
    public function filter(string $part, callable $filterMethod)
    {
        return array_filter($this->array[$part], $filterMethod);
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
            throw new OutOfBoundsStorageException(
                sprintf(OutOfBoundsStorageException::DATA_ITEM_ID_DOESNT_EXISTS_MESSAGE, (string)$id, $part)
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
