<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataReader;

use CommissionTask\Components\CurrenciesDataReader\Exceptions\CurrenciesDataReaderException;
use CommissionTask\Components\CurrenciesDataReader\Interfaces\CurrenciesDataReader as CurrenciesDataReaderContract;
use CommissionTask\Services\Config as ConfigService;
use JsonException;

class ApiCurrenciesDataReader implements CurrenciesDataReaderContract
{
    /**
     * Create API currencies data reader instance.
     */
    public function __construct(
        private ConfigService $configService
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function readCurrenciesData(): array
    {
        $currenciesRawData = $this->readJsonString();

        return $this->parseJsonString($currenciesRawData);
    }

    /**
     * Read JSON string.
     *
     * @throws CurrenciesDataReaderException
     */
    private function readJsonString(): string
    {
        $curl = curl_init();

        curl_setopt($curl, option: CURLOPT_URL, value: $this->configService->getEnvByName('CURRENCIES_API_URL'));
        curl_setopt($curl, option: CURLOPT_RETURNTRANSFER, value: 1);

        $remainingRequestsAttemptsCount = $this->configService->getConfigByName('currenciesApi.maxRequestsAttempts');

        do {
            $currenciesData = curl_exec($curl);
        } while ($currenciesData === false && --$remainingRequestsAttemptsCount);

        curl_close($curl);

        if ($currenciesData === false) {
            throw new CurrenciesDataReaderException(CurrenciesDataReaderException::FAILED_RECEIVE_DATA_MESSAGE);
        }

        return $currenciesData;
    }

    /**
     * Parse JSON string.
     *
     * @throws CurrenciesDataReaderException
     */
    private function parseJsonString(string $currenciesData): array
    {
        try {
            $parsedCurrenciesData = json_decode($currenciesData, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $parsedCurrenciesData = null;
        }

        if ($parsedCurrenciesData === null) {
            throw new CurrenciesDataReaderException(CurrenciesDataReaderException::INVALID_JSON_DATA_MESSAGE);
        }

        return $parsedCurrenciesData;
    }
}
