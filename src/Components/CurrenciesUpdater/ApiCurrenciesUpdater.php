<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesUpdater;

use CommissionTask\Components\CurrenciesUpdater\Exceptions\CurrenciesUpdaterException;
use CommissionTask\Components\CurrenciesUpdater\Interfaces\CurrenciesUpdater as CurrenciesUpdaterContract;
use CommissionTask\Components\DataFormatter\ApiCurrenciesDataFormatter;
use CommissionTask\Entities\Currency;
use CommissionTask\Repositories\Interfaces\CurrenciesRepository;
use CommissionTask\Services\Config;
use CommissionTask\Services\Config as ConfigService;
use CommissionTask\Services\Math as MathService;

class ApiCurrenciesUpdater implements CurrenciesUpdaterContract
{
    private const BASE_CURRENCY_RATE = 1;

    /**
     * Create a new API currency saver instance.
     */
    public function __construct(
        private CurrenciesRepository $currenciesRepository,
        private ApiCurrenciesDataFormatter $apiCurrenciesDataFormatter,
        private MathService $mathService
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function updateCurrencies($currenciesData): void
    {
        $baseCurrencyCode = $currenciesData[$this->apiCurrenciesDataFormatter::BASE_CURRENCY_CODE_FIELD];
        $rates = $currenciesData[$this->apiCurrenciesDataFormatter::RATES_FIELD];

        $applicationBaseCurrencyCode = $this->getApplicationBaseCurrencyCode();

        $isNeedRateRecalculation = $baseCurrencyCode !== $applicationBaseCurrencyCode;
        if ($isNeedRateRecalculation) {
            $applicationBaseCurrencyRate = $this->getApplicationBaseCurrencyRate($rates);
        }

        $acceptableCurrenciesCodes = $this->getAcceptableCurrenciesCodes();

        foreach ($rates as $currencyCode => $currencyRate) {
            if (!in_array($currencyCode, $acceptableCurrenciesCodes, strict: true)) {
                continue;
            }

            $currencyRate = (string) $currencyRate;

            if ($isNeedRateRecalculation) {
                $currencyRate = $currencyCode === $applicationBaseCurrencyCode
                    ? self::BASE_CURRENCY_RATE
                    : $this->mathService->div($currencyRate, $applicationBaseCurrencyRate, $this->getRateScale());
            }

            $currency = new Currency(
                currencyCode: $currencyCode,
                rate: $currencyRate
            );

            $this->updateCurrency($currency);
        }
    }

    /**
     * Update currency to currencies repository.
     */
    private function updateCurrency(Currency $currency): void
    {
        $updatingCurrencyFilterMethod =
            fn (Currency $checkedCurrency) => $checkedCurrency->getCurrencyCode() === $currency->getCurrencyCode();

        $existingCurrencies = $this->currenciesRepository->filter($updatingCurrencyFilterMethod);

        if ($existingCurrencies) {
            $this->currenciesRepository->update(array_key_first($existingCurrencies), $currency);
        } else {
            $this->currenciesRepository->create($currency);
        }
    }

    /**
     * Get currency code for base currency of application.
     */
    private function getApplicationBaseCurrencyCode(): string
    {
        return ConfigService::getConfigByName('currencies.baseCurrency');
    }

    /**
     * Get currency rate for base currency of application.
     *
     * @param float[] $rates
     *
     * @throws CurrenciesUpdaterException
     */
    private function getApplicationBaseCurrencyRate(array $rates): string
    {
        $applicationBaseCurrencyCode = $this->getApplicationBaseCurrencyCode();

        if (empty($rates[$applicationBaseCurrencyCode])) {
            throw new CurrenciesUpdaterException(CurrenciesUpdaterException::NO_BASE_CURRENCY_RATE_MESSAGE);
        }

        return (string) $rates[$applicationBaseCurrencyCode];
    }

    /**
     * Get acceptable currencies codes.
     *
     * @return string[]
     */
    private function getAcceptableCurrenciesCodes(): array
    {
        $acceptableConfig = Config::getConfigByName('currencies.acceptable');

        return array_column($acceptableConfig, column_key: 'currencyCode');
    }

    /**
     * Get rate scale.
     */
    private function getRateScale(): int
    {
        return Config::getConfigByName('currencies.rateScale');
    }
}
