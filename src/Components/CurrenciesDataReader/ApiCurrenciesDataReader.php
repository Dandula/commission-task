<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataReader;

use CommissionTask\Components\CurrenciesDataReader\Exceptions\ApiCurrenciesDataReaderException;
use CommissionTask\Components\CurrenciesDataReader\Interfaces\CurrenciesDataReader as CurrenciesDataReaderContract;

class ApiCurrenciesDataReader implements CurrenciesDataReaderContract
{
    const API_URL = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';
    const MAX_REQUESTS_ATTEMPTS = 3;

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

        curl_setopt($curl, CURLOPT_URL, self::API_URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $remainingRequestsAttemptsCount = self::MAX_REQUESTS_ATTEMPTS;

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
