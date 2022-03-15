<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Repositories;

use CommissionTask\Components\Storage\ArrayStorage;
use CommissionTask\Components\Storage\Exceptions\OutOfBoundsStorageException;
use CommissionTask\Entities\Currency;
use CommissionTask\Repositories\CurrenciesRepository;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \CommissionTask\Repositories\CurrenciesRepository
 *
 */
final class CurrenciesRepositoryTest extends TestCase
{
    const CURRENCY_CODE_EUR = 'EUR';
    const CURRENCY_CODE_USD = 'USD';
    const CURRENCY_CODE_JPY = 'JPY';

    const CURRENCY_RATE_EUR         = 1;
    const CURRENCY_RATE_USD         = 1.15;
    const CURRENCY_RATE_USD_UPDATED = 1.2;
    const CURRENCY_RATE_JPY         = 0.008;

    /**
     * @var CurrenciesRepository
     */
    private $currenciesRepository;

    protected function setUp(): void
    {
        $arrayStorage = new ArrayStorage();
        $this->currenciesRepository = new CurrenciesRepository($arrayStorage);
    }

    /**
     * @return CurrenciesRepository
     */
    public function testEmpty()
    {
        $this->assertEmpty(
            $this->currenciesRepository->all()
        );

        return clone $this->currenciesRepository;
    }

    /**
     * @param CurrenciesRepository $currenciesRepository
     *
     * @return CurrenciesRepository
     *
     * @doesNotPerformAssertions
     * @depends testEmpty
     */
    public function testCreate(CurrenciesRepository $currenciesRepository)
    {
        $currenciesRepository->create($this->getCurrencyEur());
        $currenciesRepository->create($this->getCurrencyUsd());
        $currenciesRepository->create($this->getCurrencyJpy());

        return clone $currenciesRepository;
    }

    /**
     * @param CurrenciesRepository $currenciesRepository
     *
     * @depends testCreate
     */
    public function testAll(CurrenciesRepository $currenciesRepository)
    {
        $actual = $currenciesRepository->all();

        $this->assertContainsOnlyInstancesOf(Currency::class, $actual);
        $this->assertEquals([
            $this->getCurrencyEur(),
            $this->getCurrencyUsd(),
            $this->getCurrencyJpy(),
        ], $actual);
    }

    /**
     * @param callable $filterMethod
     * @param int|null $expectId
     * @param Currency|null $expectation
     * @param CurrenciesRepository $currenciesRepository
     *
     * @dataProvider dataProviderForFilterTesting
     * @depends      testCreate
     */
    public function testFilter(
        callable $filterMethod,
        $expectId,
        $expectation,
        CurrenciesRepository $currenciesRepository
    )
    {
        $actual = $currenciesRepository->filter($filterMethod);
        $filteredIds = array_keys($actual);

        $this->assertContainsOnlyInstancesOf(Currency::class, $actual);
        $this->assertEquals($expectId, array_shift($filteredIds));
        $this->assertEquals($expectation, array_shift($actual));
    }

    /**
     * @param CurrenciesRepository $currenciesRepository
     *
     * @return CurrenciesRepository
     *
     * @doesNotPerformAssertions
     * @depends testCreate
     */
    public function testUpdate(CurrenciesRepository $currenciesRepository): CurrenciesRepository
    {
        $updatedCurrencyUsd = $this->getCurrencyUsd();
        $updatedCurrencyUsd->setRate(self::CURRENCY_RATE_USD_UPDATED);

        $currenciesRepository->update(1, $updatedCurrencyUsd);

        return clone $currenciesRepository;
    }

    /**
     * @param CurrenciesRepository $currenciesRepository
     *
     * @depends testUpdate
     */
    public function testRead(CurrenciesRepository $currenciesRepository)
    {
        $actual = $currenciesRepository->read(1);

        $this->assertInstanceOf(Currency::class, $actual);
        $this->assertEquals(self::CURRENCY_RATE_USD_UPDATED, $actual->getRate());
    }

    /**
     * @param CurrenciesRepository $currenciesRepository
     *
     * @return CurrenciesRepository
     *
     * @doesNotPerformAssertions
     * @depends testCreate
     */
    public function testDelete(CurrenciesRepository $currenciesRepository): CurrenciesRepository
    {
        $currenciesRepository->delete(1);
        
        return clone $currenciesRepository;
    }

    /**
     * @param CurrenciesRepository $currenciesRepository
     *
     * @depends testDelete
     */
    public function testReadFailure(CurrenciesRepository $currenciesRepository)
    {
        $this->expectException(OutOfBoundsStorageException::class);

        $currenciesRepository->read(1);
    }

    /**
     * @param CurrenciesRepository $currenciesRepository
     *
     * @depends testDelete
     */
    public function testUpdateFailure(CurrenciesRepository $currenciesRepository)
    {
        $updatedCurrency = $this->getCurrencyUsd();

        $this->expectException(OutOfBoundsStorageException::class);

        $currenciesRepository->update(1, $updatedCurrency);
    }

    /**
     * @param CurrenciesRepository $currenciesRepository
     *
     * @return CurrenciesRepository
     *
     * @doesNotPerformAssertions
     * @depends testCreate
     */
    public function testDeleteAll(CurrenciesRepository $currenciesRepository): CurrenciesRepository
    {
        $currenciesRepository->deleteAll();

        return clone $currenciesRepository;
    }

    /**
     * @param CurrenciesRepository $currenciesRepository
     *
     * @depends testDeleteAll
     */
    public function testDeletedAll(CurrenciesRepository $currenciesRepository)
    {
        $this->assertEmpty(
            $this->currenciesRepository->all()
        );
    }

    public function dataProviderForFilterTesting(): array
    {
        $filterByCurrencyCode = function (Currency $currency) {
            return $currency->getCurrencyCode() === self::CURRENCY_CODE_USD;
        };

        $filterByIsBaseFlag = function (Currency $currency) {
            return $currency->getIsBase() === true;
        };

        $filterByRate = function (Currency $currency) {
            return $currency->getRate() < 1;
        };

        $filterWithNonExistentCurrency = function (Currency $currency) {
            return $currency->getCurrencyCode() === 'AAA';
        };

        return [
            'filter currencies with USD' => [$filterByCurrencyCode, 1, $this->getCurrencyUsd()],
            'filter currencies with only base currency' => [$filterByIsBaseFlag, 0, $this->getCurrencyEur()],
            'filter currencies cheaper than the base currency' => [$filterByRate, 2, $this->getCurrencyJpy()],
            'filter currencies with non-existent currency' => [$filterWithNonExistentCurrency, null, null],
        ];
    }

    private function getCurrencyEur(): Currency
    {
        $currency = new Currency();
        $currency->setCurrencyCode(self::CURRENCY_CODE_EUR);
        $currency->setIsBase(true);
        $currency->setRate(self::CURRENCY_RATE_EUR);
        $currency->setRateUpdatedAt(new DateTime('2022-02-24'));

        return $currency;
    }

    private function getCurrencyUsd(): Currency
    {
        $currency = new Currency();
        $currency->setCurrencyCode(self::CURRENCY_CODE_USD);
        $currency->setIsBase(false);
        $currency->setRate(self::CURRENCY_RATE_USD);
        $currency->setRateUpdatedAt(new DateTime('2022-02-24'));

        return $currency;
    }

    private function getCurrencyJpy(): Currency
    {
        $currency = new Currency();
        $currency->setCurrencyCode(self::CURRENCY_CODE_JPY);
        $currency->setIsBase(false);
        $currency->setRate(self::CURRENCY_RATE_JPY);
        $currency->setRateUpdatedAt(new DateTime('2022-02-24'));

        return $currency;
    }
}
