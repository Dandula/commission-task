<?php

declare(strict_types=1);

namespace CommissionTask\Repositories;

use CommissionTask\Components\Storage\Interfaces\Storage;
use CommissionTask\Entities\Currency;
use CommissionTask\Repositories\Interfaces\CurrenciesRepository as CurrenciesRepositoryContract;

class CurrenciesRepository implements CurrenciesRepositoryContract
{
    const CURRENCIES_PART = 'currencies';

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
        return $this->storage->findAll(self::CURRENCIES_PART);
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $id): Currency
    {
        return $this->storage->findById(self::CURRENCIES_PART, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function filter(callable $filterMethod): array
    {
        return $this->storage->filter(self::CURRENCIES_PART, $filterMethod);
    }

    /**
     * {@inheritDoc}
     */
    public function create(Currency $currency)
    {
        $this->storage->create(self::CURRENCIES_PART, $currency);
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, Currency $currency)
    {
        $this->storage->update(self::CURRENCIES_PART, $id, $currency);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id)
    {
        $this->storage->delete(self::CURRENCIES_PART, $id);
    }
}
