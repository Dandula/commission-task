<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Components\CurrenciesDataReader\Interfaces\CurrenciesDataReader;
use CommissionTask\Components\CurrenciesDataValidator\Interfaces\CurrenciesDataValidator;
use CommissionTask\Components\CurrenciesUpdater\Interfaces\CurrenciesUpdater;
use CommissionTask\Exceptions\CommissionTaskException;
use CommissionTask\Repositories\Interfaces\CurrenciesRepository;
use CommissionTask\Services\Math as MathService;

class Currency
{
    public const BASE_CURRENCY_RATE = 1;

    /**
     * Create currency service instance.
     */
    public function __construct(
        private CurrenciesRepository $currenciesRepository,
        private CurrenciesDataReader $currenciesDataReader,
        private CurrenciesDataValidator $currenciesDataValidator,
        private CurrenciesUpdater $currenciesUpdater,
        private MathService $mathService
    ) {
    }

    /**
     * Convert amount to given currency by given currency code.
     */
    public function convertAmountToCurrency(
        string $amount,
        string $fromCurrencyCode,
        string $toCurrencyCode,
        int $scale
    ): string {
        if ($fromCurrencyCode !== $toCurrencyCode) {
            $fromCurrencyRate = $this->getCurrencyRate($fromCurrencyCode);
            $toCurrencyRate = $this->getCurrencyRate($toCurrencyCode);
            $relativeRate = $toCurrencyRate / $fromCurrencyRate;
        } else {
            $relativeRate = self::BASE_CURRENCY_RATE;
        }

        return $this->mathService->mul($amount, (string) $relativeRate, $scale);
    }

    /**
     * Get currency rate by given currency code.
     *
     * @throws CommissionTaskException
     */
    public function getCurrencyRate(string $currencyCode, bool $forcedCurrentRate = false): float
    {
        $currency = $this->currenciesRepository->getCurrencyByCode($currencyCode);

        if ($currency !== null) {
            return $currency->getRate();
        }

        if ($forcedCurrentRate) {
            throw new CommissionTaskException(sprintf(CommissionTaskException::UNDEFINED_CURRENCY_RATE_MESSAGE, $currencyCode));
        }

        $this->updateCurrenciesRates();

        return $this->getCurrencyRate($currencyCode, forcedCurrentRate: true);
    }

    /**
     * Update currencies rates.
     */
    private function updateCurrenciesRates(): void
    {
        $currenciesData = $this->currenciesDataReader->readCurrenciesData();
        $this->currenciesDataValidator->validateCurrenciesData($currenciesData);
        $this->currenciesUpdater->updateCurrencies($currenciesData);
    }
}
