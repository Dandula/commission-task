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
    public const DEFAULT_SCALE_AMOUNT = 2;

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
            $relativeRate = $this->mathService->div(
                $this->getCurrencyRate($toCurrencyCode),
                $this->getCurrencyRate($fromCurrencyCode),
                $this->getRateScale()
            );
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
    public function getCurrencyRate(string $currencyCode, bool $forcedCurrentRate = false): string
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
     * Get currency scale by given currency code.
     *
     * @throws CommissionTaskException
     */
    public function getCurrencyScale(string $currencyCode): int
    {
        $filteredCurrenciesConfig = array_filter(
            $this->getAcceptableCurrenciesConfig(),
            static fn (array $acceptableCurrencyConfig) => $acceptableCurrencyConfig['currencyCode'] === $currencyCode
        );

        $currencyConfig = array_shift($filteredCurrenciesConfig);

        return $currencyConfig['scale'] ?? self::DEFAULT_SCALE_AMOUNT;
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

    /**
     * Get acceptable currencies codes.
     *
     * @return string[]
     */
    public function getAcceptableCurrenciesCodes(): array
    {
        $acceptableConfig = $this->getAcceptableCurrenciesConfig();

        return array_column($acceptableConfig, column_key: 'currencyCode');
    }

    /**
     * Get acceptable currencies config.
     */
    private function getAcceptableCurrenciesConfig(): array
    {
        return Config::getConfigByName('currencies.acceptable');
    }

    /**
     * Get rate scale.
     */
    private function getRateScale(): int
    {
        return Config::getConfigByName('currencies.rateScale');
    }
}
