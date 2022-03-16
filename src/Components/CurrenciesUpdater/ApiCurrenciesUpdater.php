<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesUpdater;

use CommissionTask\Components\CurrenciesUpdater\Exceptions\CurrenciesUpdaterException;
use CommissionTask\Components\CurrenciesUpdater\Interfaces\CurrenciesUpdater as CurrenciesUpdaterContract;
use CommissionTask\Components\DataFormatter\ApiCurrenciesDataFormatter;
use CommissionTask\Components\DataFormatter\CurrenciesUpdaterDataFormatter;
use CommissionTask\Entities\Currency;
use CommissionTask\Repositories\Interfaces\CurrenciesRepository;
use CommissionTask\Services\Config as ConfigService;
use CommissionTask\Services\Date as DateService;

class ApiCurrenciesUpdater implements CurrenciesUpdaterContract
{
    /**
     * Create a new API currency saver instance.
     */
    public function __construct(
        private CurrenciesRepository $currenciesRepository,
        private ApiCurrenciesDataFormatter $apiCurrenciesDataFormatter,
        private CurrenciesUpdaterDataFormatter $currenciesUpdaterDataFormatter,
        private DateService $dateService
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function updateCurrencies($currenciesData): void
    {
        $baseCurrencyCode = $currenciesData[$this->apiCurrenciesDataFormatter::BASE_CURRENCY_CODE_FIELD];
        $rateUpdatedAt = $this->dateService->parseDate(
            $currenciesData[$this->apiCurrenciesDataFormatter::DATE_FIELD],
            $this->apiCurrenciesDataFormatter::DATE_FORMAT
        );
        $rates = $currenciesData[$this->apiCurrenciesDataFormatter::RATES_FIELD];

        $applicationBaseCurrencyCode = $this->getApplicationBaseCurrencyCode();

        $isNeedRateRecalculation = $baseCurrencyCode !== $applicationBaseCurrencyCode;
        if ($isNeedRateRecalculation) {
            $applicationBaseCurrencyRate = $this->getApplicationBaseCurrencyRate($rates);
        }

        foreach ($rates as $currencyCode => $currencyRate) {
            $isBase = $currencyCode === $applicationBaseCurrencyCode;

            if ($isNeedRateRecalculation) {
                $currencyRate = $isBase ? self::BASE_CURRENCY_RATE : $currencyRate / $applicationBaseCurrencyRate;
            }

            $currency = new Currency(
                currencyCode: $currencyCode,
                isBase: $isBase,
                rate: $currencyRate,
                rateUpdatedAt: $rateUpdatedAt
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
            $this->currenciesRepository->update(array_keys($existingCurrencies)[0], $currency);
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
    private function getApplicationBaseCurrencyRate(array $rates): float
    {
        $applicationBaseCurrencyCode = $this->getApplicationBaseCurrencyCode();

        if (empty($rates[$applicationBaseCurrencyCode])) {
            throw new CurrenciesUpdaterException(CurrenciesUpdaterException::NO_BASE_CURRENCY_RATE_MESSAGE);
        }

        return $rates[$applicationBaseCurrencyCode];
    }

    /**
     * Make currency instance.
     */
    private function makeCurrency(array $currencyData): Currency
    {
        return new Currency(
            $currencyData[$this->currenciesUpdaterDataFormatter::CURRENCIES_RATES_CURRENCY_CODE],
            $currencyData[$this->currenciesUpdaterDataFormatter::CURRENCIES_RATES_IS_BASE],
            $currencyData[$this->currenciesUpdaterDataFormatter::CURRENCIES_RATES_RATE],
            $currencyData[$this->currenciesUpdaterDataFormatter::CURRENCIES_RATES_RATE_UPDATED_AT]
        );
    }
}
