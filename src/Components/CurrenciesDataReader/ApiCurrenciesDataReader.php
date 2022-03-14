<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataReader;

use CommissionTask\Components\CurrenciesDataReader\Exceptions\ApiCurrenciesDataReaderException;
use CommissionTask\Components\CurrenciesDataReader\Interfaces\CurrenciesDataReader as CurrenciesDataReaderContract;
use CommissionTask\Services\Config as ConfigService;

class ApiCurrenciesDataReader implements CurrenciesDataReaderContract
{
    /**
     * Create a new API currencies data reader instance.
     */
    public function __construct()
    {
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
     * @throws ApiCurrenciesDataReaderException
     */
    private function readJsonString(): string
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, ConfigService::getEnvByName('CURRENCIES_API_URL'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $remainingRequestsAttemptsCount = ConfigService::getConfigByName('currenciesApi.maxRequestsAttempts');

        do {
            $currenciesData = curl_exec($curl);
        } while ($currenciesData === false && --$remainingRequestsAttemptsCount);

        curl_close($curl);

        if ($currenciesData === false) {
            throw new ApiCurrenciesDataReaderException(ApiCurrenciesDataReaderException::FAILED_RECEIVE_DATA_MESSAGE);
        }

        return $currenciesData;
    }

    /**
     * Parse JSON string.
     *
     * @throws ApiCurrenciesDataReaderException
     */
    private function parseJsonString(string $currenciesData): array
    {
        $currenciesData = json_decode($currenciesData, true);

        if ($currenciesData === null) {
            throw new ApiCurrenciesDataReaderException(ApiCurrenciesDataReaderException::INVALID_JSON_DATA_MESSAGE);
        }

        return $currenciesData;
    }
}
