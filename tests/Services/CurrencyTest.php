<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Services;

use CommissionTask\Components\CurrenciesDataReader\Interfaces\CurrenciesDataReader;
use CommissionTask\Components\CurrenciesDataValidator\Interfaces\CurrenciesDataValidator;
use CommissionTask\Components\CurrenciesUpdater\Interfaces\CurrenciesUpdater;
use CommissionTask\Entities\Currency;
use CommissionTask\Exceptions\CommissionTaskException;
use CommissionTask\Repositories\Interfaces\CurrenciesRepository;
use CommissionTask\Services\Currency as CurrencyService;
use CommissionTask\Services\Date as DateService;
use CommissionTask\Services\Math as MathService;
use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass CurrencyService
 */
final class CurrencyTest extends TestCase
{
    const CURRENCY_CODE_EUR = 'EUR';
    const CURRENCY_CODE_USD = 'USD';

    const CURRENCY_RATE_EUR = 1;
    const CURRENCY_RATE_USD = 1.15;

    const RELATIVE_DATETIME_NOW = 'now';

    /**
     * @var CurrenciesDataReader
     */
    private $mockCurrenciesDataReader;

    /**
     * @var CurrenciesDataValidator
     */
    private $mockCurrenciesDataValidator;

    /**
     * @var CurrenciesDataValidator
     */
    private $mockCurrenciesUpdater;

    /**
     * @var DateService
     */
    private $dateService;

    public function setUp()
    {
        $this->mockCurrenciesDataReader = $this->getMockBuilder(CurrenciesDataReader::class)
            ->setMethods(['readCurrenciesData'])
            ->getMock();

        $this->mockCurrenciesDataReader->expects($this->any())
            ->method('readCurrenciesData');

        $this->mockCurrenciesDataValidator = $this->getMockBuilder(CurrenciesDataValidator::class)
            ->setMethods(['validateCurrenciesData'])
            ->getMock();

        $this->mockCurrenciesDataValidator->expects($this->any())
            ->method('validateCurrenciesData');

        $this->mockCurrenciesUpdater = $this->getMockBuilder(CurrenciesUpdater::class)
            ->setMethods(['updateCurrencies'])
            ->getMock();

        $this->mockCurrenciesUpdater->expects($this->any())
            ->method('updateCurrencies');

        $this->dateService = new DateService();
    }

    public function tearDown()
    {
        unset($this->mockCurrenciesDataReader);
        unset($this->mockCurrenciesDataValidator);
        unset($this->mockCurrenciesUpdater);
        unset($this->dateService);
    }

    /**
     * @param string $currencyCode
     * @param string $relativeDatetime
     * @param float $expectation
     *
     * @dataProvider dataProviderForGetCurrencyRateFromExistingTesting
     */
    public function testGetCurrencyRateFromExisting(string $currencyCode, string $relativeDatetime, float $expectation)
    {
        $mockCurrenciesRepository = $this->getMockCurrenciesRepository();

        $mockCurrenciesRepository->expects($this->any())
            ->method('filter')
            ->willReturn($this->getFilteredCurrenciesUsd($relativeDatetime));

        $currencyService = $this->getCurrencyService($mockCurrenciesRepository);

        $this->assertEquals(
            $expectation,
            $currencyService->getCurrencyRate($currencyCode)
        );
    }

    /**
     * @param string $currencyCode
     * @param string $relativeDatetime
     * @param float $expectation
     *
     * @dataProvider dataProviderForGetCurrencyRateFromNotExistingTesting
     */
    public function testGetCurrencyRateFromNotExisting(string $currencyCode, string $relativeDatetime, float $expectation)
    {
        $mockCurrenciesRepository = $this->getMockCurrenciesRepository();

        $mockCurrenciesRepository->expects($this->at(0))
            ->method('filter')
            ->willReturn($this->getEmptyCurrencies());

        $mockCurrenciesRepository->expects($this->at(1))
            ->method('filter')
            ->willReturn($this->getFilteredCurrenciesUsd($relativeDatetime));

        $currencyService = $this->getCurrencyService($mockCurrenciesRepository);

        $this->assertEquals(
            $expectation,
            $currencyService->getCurrencyRate($currencyCode)
        );
    }

    /**
     * @param string $currencyCode
     * @param string $expectException
     * @param string $expectExceptionMessage
     *
     * @dataProvider dataProviderForGetCurrencyRateFailureTesting
     */
    public function testGetCurrencyRateFailure(string $currencyCode, string $expectException, string $expectExceptionMessage)
    {
        $mockCurrenciesRepository = $this->getMockCurrenciesRepository();

        $mockCurrenciesRepository->expects($this->any())
            ->method('filter')
            ->willReturn($this->getEmptyCurrencies());

        $this->expectException($expectException);
        $this->expectExceptionMessage(sprintf($expectExceptionMessage, $currencyCode));

        $currencyService = $this->getCurrencyService($mockCurrenciesRepository);

        $currencyService->getCurrencyRate($currencyCode);
    }

    /**
     * @param string $amount
     * @param string $fromCurrencyCode
     * @param string $toCurrencyCode
     * @param int $scale
     * @param string $expectation
     *
     * @dataProvider dataProviderForConvertAmountToCurrencyDifferentTesting
     */
    public function testConvertAmountToCurrencyDifferent(
        string $amount,
        string $fromCurrencyCode,
        string $toCurrencyCode,
        int $scale,
        string $expectation
    )
    {
        $mockCurrenciesRepository = $this->getMockCurrenciesRepository();

        $mockCurrenciesRepository->expects($this->at(0))
            ->method('filter')
            ->willReturn($this->getFilteredCurrenciesEur(self::RELATIVE_DATETIME_NOW));

        $mockCurrenciesRepository->expects($this->at(1))
            ->method('filter')
            ->willReturn($this->getFilteredCurrenciesUsd(self::RELATIVE_DATETIME_NOW));

        $currencyService = $this->getCurrencyService($mockCurrenciesRepository);

        $mathService = new MathService($scale);

        $this->assertEquals(
            $expectation,
            $currencyService->convertAmountToCurrency($amount, $fromCurrencyCode, $toCurrencyCode, $mathService)
        );
    }

    /**
     * @param string $amount
     * @param string $fromCurrencyCode
     * @param string $toCurrencyCode
     * @param int $scale
     * @param string $expectation
     *
     * @dataProvider dataProviderForConvertAmountToCurrencySameTesting
     */
    public function testConvertAmountToCurrencySame(
        string $amount,
        string $fromCurrencyCode,
        string $toCurrencyCode,
        int $scale,
        string $expectation
    )
    {
        $mockCurrenciesRepository = $this->getMockCurrenciesRepository();

        $mockCurrencyService = $this->getMockBuilder(CurrencyService::class)
            ->setConstructorArgs([
                $mockCurrenciesRepository,
                $this->mockCurrenciesDataReader,
                $this->mockCurrenciesDataValidator,
                $this->mockCurrenciesUpdater,
                $this->dateService
            ])
            ->setMethods([
                'getCurrencyRate',
            ])
            ->getMock();

        $mockCurrencyService->expects($this->never())
            ->method('getCurrencyRate');

        $mathService = new MathService($scale);

        $this->assertEquals(
            $expectation,
            $mockCurrencyService->convertAmountToCurrency($amount, $fromCurrencyCode, $toCurrencyCode, $mathService)
        );
    }

    public function dataProviderForGetCurrencyRateFromExistingTesting(): array
    {
        return [
            'get rate of currency from existing in repository and actual' => [self::CURRENCY_CODE_USD, '2 hours', 1.15],
            'get rate currency from existing in repository and not actual' => [self::CURRENCY_CODE_USD, '5 days', 1.15],
        ];
    }

    public function dataProviderForGetCurrencyRateFromNotExistingTesting(): array
    {
        return [
            'get rate of currency from not existing in repository and actual' => [self::CURRENCY_CODE_USD, '2 hours', 1.15],
            'get rate currency from not existing in repository and not actual' => [self::CURRENCY_CODE_USD, '5 days', 1.15],
        ];
    }

    public function dataProviderForGetCurrencyRateFailureTesting(): array
    {
        return [
            'try to get rate currency after an update failure' => [
                self::CURRENCY_CODE_USD,
                CommissionTaskException::class,
                CommissionTaskException::UNDEFINED_CURRENCY_RATE_MESSAGE
            ],
        ];
    }

    public function dataProviderForConvertAmountToCurrencyDifferentTesting(): array
    {
        return [
            'convert amount from one currency to another' => [
                '1000.00',
                self::CURRENCY_CODE_EUR,
                self::CURRENCY_CODE_USD,
                2,
                '1150.00'
            ],
        ];
    }

    public function dataProviderForConvertAmountToCurrencySameTesting(): array
    {
        return [
            'convert amount from one currency to another' => [
                '1000.00',
                self::CURRENCY_CODE_USD,
                self::CURRENCY_CODE_USD,
                2,
                '1000.00'
            ],
        ];
    }

    /**
     * @param CurrenciesRepository|MockObject $mockCurrenciesRepository
     */
    private function getCurrencyService($mockCurrenciesRepository): CurrencyService
    {
        return new CurrencyService(
            $mockCurrenciesRepository,
            $this->mockCurrenciesDataReader,
            $this->mockCurrenciesDataValidator,
            $this->mockCurrenciesUpdater,
            $this->dateService
        );
    }

    /**
     * @return CurrenciesRepository|MockObject
     */
    private function getMockCurrenciesRepository()
    {
        return $this->getMockBuilder(CurrenciesRepository::class)
            ->setMethods([
                'all',
                'filter',
                'read',
                'create',
                'update',
                'delete',
                'deleteAll',
            ])
            ->getMock();
    }

    /**
     * @return Currency[]
     */
    private function getEmptyCurrencies(): array
    {
        return [];
    }

    /**
     * @return Currency[]
     */
    private function getFilteredCurrencies(
        string $currencyCode,
        float $rate,
        string $rateUpdatedAtDatetimeString
    ): array
    {
        $rateUpdatedAt = (new DateTime())->modify($rateUpdatedAtDatetimeString);

        $currency = new Currency();
        $currency->setCurrencyCode($currencyCode);
        $currency->setIsBase(true);
        $currency->setRate($rate);
        $currency->setRateUpdatedAt($rateUpdatedAt);

        return [$currency];
    }

    /**
     * @return Currency[]
     */
    private function getFilteredCurrenciesEur(string $rateUpdatedAtDatetimeString): array
    {
        $rateUpdatedAt = (new DateTime())->modify($rateUpdatedAtDatetimeString);

        $currency = new Currency();
        $currency->setCurrencyCode(self::CURRENCY_CODE_EUR);
        $currency->setIsBase(true);
        $currency->setRate(self::CURRENCY_RATE_EUR);
        $currency->setRateUpdatedAt($rateUpdatedAt);

        return [$currency];
    }

    /**
     * @return Currency[]
     */
    private function getFilteredCurrenciesUsd(string $rateUpdatedAtDatetimeString): array
    {
        $rateUpdatedAt = (new DateTime())->modify($rateUpdatedAtDatetimeString);

        $currency = new Currency();
        $currency->setCurrencyCode(self::CURRENCY_CODE_USD);
        $currency->setIsBase(true);
        $currency->setRate(self::CURRENCY_RATE_USD);
        $currency->setRateUpdatedAt($rateUpdatedAt);

        return [$currency];
    }
}
