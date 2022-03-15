<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataReader;

use CommissionTask\Components\CurrenciesDataReader\Exceptions\ApiCurrenciesDataReaderException;
use CommissionTask\Components\CurrenciesDataReader\Interfaces\CurrenciesDataReader as CurrenciesDataReaderContract;
use CommissionTask\Services\Config as ConfigService;
use JsonException;

class ApiCurrenciesDataReader implements CurrenciesDataReaderContract
{
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

        curl_setopt($curl, option: CURLOPT_URL, value: ConfigService::getEnvByName('CURRENCIES_API_URL'));
        curl_setopt($curl, option: CURLOPT_RETURNTRANSFER, value: 1);

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
        try {
            $parsedCurrenciesData = json_decode($currenciesData, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $parsedCurrenciesData = null;
        }

        if ($parsedCurrenciesData === null) {
            throw new ApiCurrenciesDataReaderException(ApiCurrenciesDataReaderException::INVALID_JSON_DATA_MESSAGE);
        }

        return $parsedCurrenciesData;
    }
}
