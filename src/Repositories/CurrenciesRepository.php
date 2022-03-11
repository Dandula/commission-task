<?php

declare(strict_types=1);

namespace CommissionTask\Repositories;

use CommissionTask\Components\Storage\Interfaces\Storage;
use CommissionTask\Entities\Currency;
use CommissionTask\Repositories\Interfaces\CurrenciesRepository as CurrenciesRepositoryContract;

class CurrenciesRepository implements CurrenciesRepositoryContract
{
    const REPOSITORY_PART = 'currencies';

    /**
     * @var Storage
     */
    private $storage;

    /**
     * Create currencies repository instance.
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritDoc}
     */
    public function all(): array
    {
        return $this->storage->findAll(self::REPOSITORY_PART);
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $id): Currency
    {
        return $this->storage->findById(self::REPOSITORY_PART, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function filter(callable $filterMethod): array
    {
        return $this->storage->filter(self::REPOSITORY_PART, $filterMethod);
    }

    /**
     * {@inheritDoc}
     */
    public function create(Currency $currency)
    {
        $this->storage->create(self::REPOSITORY_PART, $currency);
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, Currency $currency)
    {
        $this->storage->update(self::REPOSITORY_PART, $id, $currency);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id)
    {
        $this->storage->delete(self::REPOSITORY_PART, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAll()
    {
        $this->storage->deleteAll(self::REPOSITORY_PART);
    }
}
