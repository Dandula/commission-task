<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Components\CurrenciesDataReader\Exceptions\Interfaces\CurrenciesDataReaderException;
use CommissionTask\Components\CurrenciesDataReader\Interfaces\CurrenciesDataReader;
use CommissionTask\Components\CurrenciesDataValidator\Exceptions\Interfaces\CurrenciesDataValidatorException;
use CommissionTask\Components\CurrenciesDataValidator\Interfaces\CurrenciesDataValidator;
use CommissionTask\Components\CurrenciesUpdater\Exceptions\Interfaces\CurrenciesUpdaterException;
use CommissionTask\Components\CurrenciesUpdater\Interfaces\CurrenciesUpdater;
use CommissionTask\Entities\Currency as CurrencyEntity;
use CommissionTask\Exceptions\CommissionTaskException;
use CommissionTask\Repositories\Interfaces\CurrenciesRepository;
use CommissionTask\Services\Date as DateService;
use CommissionTask\Services\Math as MathService;

class Currency
{
    const BASE_CURRENCY_RATE = 1;

    const ACTUAL_RATE_PERIOD = '1 day';

    /**
     * @var CurrenciesRepository
     */
    private $currenciesRepository;

    /**
     * @var CurrenciesDataReader
     */
    private $currenciesDataReader;

    /**
     * @var CurrenciesDataValidator
     */
    private $currenciesDataValidator;

    /**
     * @var CurrenciesUpdater
     */
    private $currenciesUpdater;

    /**
     * @var DateService
     */
    private $dateService;

    /**
     * Create currency service instance.
     */
    public function __construct(
        CurrenciesRepository $currenciesRepository,
        CurrenciesDataReader $currenciesDataReader,
        CurrenciesDataValidator $currenciesDataValidator,
        CurrenciesUpdater $currenciesUpdater,
        DateService $dateService
    ) {
        $this->currenciesRepository = $currenciesRepository;
        $this->currenciesDataReader = $currenciesDataReader;
        $this->currenciesDataValidator = $currenciesDataValidator;
        $this->currenciesUpdater = $currenciesUpdater;
        $this->dateService = $dateService;
    }

    /**
     * Convert amount to given currency by given currency code.
     */
    public function convertAmountToCurrency(
        string $amount,
        string $fromCurrencyCode,
        string $toCurrencyCode,
        MathService $mathService
    ): string {
        if ($fromCurrencyCode !== $toCurrencyCode) {
            $fromCurrencyRate = $this->getCurrencyRate($fromCurrencyCode);
            $toCurrencyRate = $this->getCurrencyRate($toCurrencyCode);
            $relativeRate = $toCurrencyRate / $fromCurrencyRate;
        } else {
            $relativeRate = self::BASE_CURRENCY_RATE;
        }

        return $mathService->mul($amount, (string) $relativeRate);
    }

    /**
     * Get currency rate by given currency code.
     *
     * @throws CommissionTaskException|CurrenciesDataValidatorException
     */
    public function getCurrencyRate(string $currencyCode, bool $forcedCurrentRate = false): float
    {
        $currenciesFilterMethod = function (CurrencyEntity $currency) use ($currencyCode) {
            return $currency->getCurrencyCode() === $currencyCode;
        };

        $currencies = $this->currenciesRepository->filter($currenciesFilterMethod);
        $currency = array_shift($currencies);

        if ($currency === null || !$this->isActualRate($currency)) {
            if ($forcedCurrentRate) {
                throw new CommissionTaskException(sprintf(CommissionTaskException::UNDEFINED_CURRENCY_RATE_MESSAGE, $currencyCode));
            }

            $this->updateCurrenciesRates();

            return $this->getCurrencyRate($currencyCode, true);
        }

        return $currency->getRate();
    }

    /**
     * Update currencies rates.
     *
     * @return void
     *
     * @throws CurrenciesDataReaderException|CurrenciesDataValidatorException|CurrenciesUpdaterException
     */
    private function updateCurrenciesRates()
    {
        $currenciesData = $this->currenciesDataReader->readCurrenciesData();
        $this->currenciesDataValidator->validateCurrenciesData($currenciesData);
        $this->currenciesUpdater->updateCurrencies($currenciesData);
    }

    /**
     * Check is actual rate of currency by given currency.
     */
    private function isActualRate(CurrencyEntity $currency): bool
    {
        $expirationDate = $this->dateService->subInterval(
            $this->dateService->getNow(),
            self::ACTUAL_RATE_PERIOD
        );

        return $currency->getRateUpdatedAt() > $expirationDate;
    }
}
