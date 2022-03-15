<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Factories;

use CommissionTask\Components\DataFormatter\CurrenciesUpdaterDataFormatter;
use CommissionTask\Entities\Currency;
use CommissionTask\Factories\ApiCurrencyFactory;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass ApiCurrencyFactory
 */
final class ApiCurrencyFactoryTest extends TestCase
{
    /**
     * @var ApiCurrencyFactory
     */
    private $apiCurrencyFactory;

    protected function setUp(): void
    {
        $currenciesUpdaterDataFormatter = new CurrenciesUpdaterDataFormatter();
        $this->apiCurrencyFactory = new ApiCurrencyFactory($currenciesUpdaterDataFormatter);
    }

    /**
     * @param mixed $currencyData
     * @param string $expectCurrencyCode
     * @param bool $expectIsBase
     * @param float $expectRate
     * @param DateTime $rateUpdatedAt
     *
     * @dataProvider dataProviderForMakeCurrencyTesting
     */
    public function testMakeCurrency(
        $currencyData,
        string $expectCurrencyCode,
        bool $expectIsBase,
        float $expectRate,
        DateTime $rateUpdatedAt
    )
    {
        $actual = $this->apiCurrencyFactory->makeCurrency($currencyData);

        $this->assertInstanceOf(Currency::class, $actual);
        $this->assertEquals($expectCurrencyCode, $actual->getCurrencyCode());
        $this->assertEquals($expectIsBase, $actual->getIsBase());
        $this->assertEquals($expectRate, $actual->getRate());
        $this->assertEquals($rateUpdatedAt, $actual->getRateUpdatedAt());
    }

    public function dataProviderForMakeCurrencyTesting(): array
    {
        $currenciesUpdaterDataFormatter = new CurrenciesUpdaterDataFormatter();

        $rateUpdatedAt = new DateTime();

        return [
            'make base currency EUR' => [
                [
                    $currenciesUpdaterDataFormatter::CURRENCIES_RATES_CURRENCY_CODE => 'EUR',
                    $currenciesUpdaterDataFormatter::CURRENCIES_RATES_IS_BASE => true,
                    $currenciesUpdaterDataFormatter::CURRENCIES_RATES_RATE => 1.00,
                    $currenciesUpdaterDataFormatter::CURRENCIES_RATES_RATE_UPDATED_AT => $rateUpdatedAt,
                ],
                'EUR', true, 1.00, $rateUpdatedAt
            ],
            'make currency USD' => [
                [
                    $currenciesUpdaterDataFormatter::CURRENCIES_RATES_CURRENCY_CODE => 'USD',
                    $currenciesUpdaterDataFormatter::CURRENCIES_RATES_IS_BASE => false,
                    $currenciesUpdaterDataFormatter::CURRENCIES_RATES_RATE => 1.15,
                    $currenciesUpdaterDataFormatter::CURRENCIES_RATES_RATE_UPDATED_AT => $rateUpdatedAt,
                ],
                'USD', false, 1.15, $rateUpdatedAt
            ],
        ];
    }
}
