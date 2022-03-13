<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesUpdater;

use CommissionTask\Components\CurrenciesUpdater\Exceptions\ApiCurrenciesUpdaterException;
use CommissionTask\Components\CurrenciesUpdater\Interfaces\CurrenciesUpdater as CurrenciesUpdaterContract;
use CommissionTask\Components\DataFormatter\ApiCurrenciesDataFormatter;
use CommissionTask\Components\DataFormatter\CurrenciesUpdaterDataFormatter;
use CommissionTask\Entities\Currency;
use CommissionTask\Factories\Interfaces\CurrencyFactory;
use CommissionTask\Repositories\Interfaces\CurrenciesRepository;
use CommissionTask\Services\Date as DateService;

class ApiCurrenciesUpdater implements CurrenciesUpdaterContract
{
    /**
     * @var CurrenciesRepository
     */
    private $currenciesRepository;

    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var ApiCurrenciesDataFormatter
     */
    private $apiCurrenciesDataFormatter;

    /**
     * @var CurrenciesUpdaterDataFormatter
     */
    private $currenciesUpdaterDataFormatter;

    /**
     * @var DateService
     */
    private $dateService;

    /**
     * Create a new API currency saver instance.
     */
    public function __construct(
        CurrenciesRepository $currenciesRepository,
        CurrencyFactory $currencyFactory,
        ApiCurrenciesDataFormatter $apiCurrenciesDataFormatter,
        CurrenciesUpdaterDataFormatter $currenciesUpdaterDataFormatter,
        DateService $dateService
    ) {
        $this->currenciesRepository = $currenciesRepository;
        $this->currencyFactory = $currencyFactory;
        $this->apiCurrenciesDataFormatter = $apiCurrenciesDataFormatter;
        $this->currenciesUpdaterDataFormatter = $currenciesUpdaterDataFormatter;
        $this->dateService = $dateService;
    }

    /**
     * {@inheritDoc}
     */
    public function updateCurrencies($currenciesData)
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
                if ($isBase) {
                    $currencyRate = self::BASE_CURRENCY_RATE;
                } else {
                    $currencyRate /= $applicationBaseCurrencyRate;
                }
            }

            $currencyData = [
                $this->currenciesUpdaterDataFormatter::CURRENCIES_RATES_CURRENCY_CODE => $currencyCode,
                $this->currenciesUpdaterDataFormatter::CURRENCIES_RATES_IS_BASE => $isBase,
                $this->currenciesUpdaterDataFormatter::CURRENCIES_RATES_RATE => $currencyRate,
                $this->currenciesUpdaterDataFormatter::CURRENCIES_RATES_RATE_UPDATED_AT => $rateUpdatedAt,
            ];

            $this->updateCurrency($currencyData);
        }
    }

    /**
     * Update currency to currencies repository.
     *
     * @param mixed $currencyData
     *
     * @return void
     */
    private function updateCurrency($currencyData)
    {
        $currency = $this->currencyFactory->makeCurrency($currencyData);

        $currencyCode = $currency->getCurrencyCode();

        $updatingCurrencyFilterMethod = function (Currency $checkedCurrency) use ($currencyCode) {
            return $checkedCurrency->getCurrencyCode() === $currencyCode;
        };

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
        return self::BASE_CURRENCY_CODE;
    }

    /**
     * Get currency rate for base currency of application.
     *
     * @param float[] $rates
     *
     * @throws ApiCurrenciesUpdaterException
     */
    private function getApplicationBaseCurrencyRate(array $rates): float
    {
        $applicationBaseCurrencyCode = $this->getApplicationBaseCurrencyCode();

        if (empty($rates[$applicationBaseCurrencyCode])) {
            throw new ApiCurrenciesUpdaterException(ApiCurrenciesUpdaterException::NO_BASE_CURRENCY_RATE_MESSAGE);
        }

        return $rates[$applicationBaseCurrencyCode];
    }
}
