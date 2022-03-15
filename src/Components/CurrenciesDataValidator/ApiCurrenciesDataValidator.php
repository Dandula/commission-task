<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidator;

use CommissionTask\Components\CurrenciesDataValidator\Exceptions\ApiCurrenciesDataValidatorException;
use CommissionTask\Components\CurrenciesDataValidator\Exceptions\CurrenciesDataValidatorException;
use CommissionTask\Components\CurrenciesDataValidator\Interfaces\CurrenciesDataValidator as CurrenciesDataValidatorsContract;
use CommissionTask\Components\CurrenciesDataValidator\Traits\FieldFormat;
use CommissionTask\Components\DataFormatter\ApiCurrenciesDataFormatter;
use CommissionTask\Services\Date as DateService;

class ApiCurrenciesDataValidator implements CurrenciesDataValidatorsContract
{
    use FieldFormat;

    private array $validatedData;

    /**
     * Create CSV transaction data validator instance.
     */
    public function __construct(
        private ApiCurrenciesDataFormatter $apiCurrenciesDataFormatter,
        private DateService $dateService
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function validateCurrenciesData(array $currenciesData): void
    {
        $this->validatedData = $currenciesData;

        $this->validateMainFields()
            ->validateCurrenciesRates();
    }

    /**
     * Validate main fields.
     *
     * @return $this
     *
     * @throws ApiCurrenciesDataValidatorException|CurrenciesDataValidatorException
     */
    private function validateMainFields(): self
    {
        return $this->validateMainFieldsExistence()
            ->validateBaseCurrencyCodeMainField()
            ->validateDateMainField()
            ->validateRatesMainField();
    }

    /**
     * Validate main fields existence.
     *
     * @return $this
     *
     * @throws ApiCurrenciesDataValidatorException
     */
    private function validateMainFieldsExistence(): self
    {
        if (array_diff(
            $this->apiCurrenciesDataFormatter::MAIN_FIELDS,
            array_keys($this->validatedData)
        )) {
            throw new ApiCurrenciesDataValidatorException(ApiCurrenciesDataValidatorException::NO_REQUIRED_FIELDS_MESSAGE);
        }

        return $this;
    }

    /**
     * Validate base currency code at main fields.
     *
     * @return $this
     *
     * @throws ApiCurrenciesDataValidatorException
     */
    private function validateBaseCurrencyCodeMainField(): self
    {
        return $this->validateCurrencyCodeField(
            $this->validatedData[$this->apiCurrenciesDataFormatter::BASE_CURRENCY_CODE_FIELD]
        );
    }

    /**
     * Validate date at main fields.
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateDateMainField(): self
    {
        return $this->validateDateField(
            $this->validatedData[$this->apiCurrenciesDataFormatter::DATE_FIELD],
            $this->apiCurrenciesDataFormatter::DATE_FORMAT
        );
    }

    /**
     * Validate rates at main fields.
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateRatesMainField(): self
    {
        return $this->validateIsArrayField($this->validatedData[$this->apiCurrenciesDataFormatter::RATES_FIELD]);
    }

    /**
     * Validate currencies rates fields.
     *
     * @return $this
     *
     * @throws ApiCurrenciesDataValidatorException|CurrenciesDataValidatorException
     */
    private function validateCurrenciesRates(): self
    {
        $currenciesRatesData = $this->validatedData[$this->apiCurrenciesDataFormatter::RATES_FIELD];

        foreach ($currenciesRatesData as $currencyCode => $currencyRate) {
            $this->validateCurrencyCodeField($currencyCode)
                ->validateCurrencyRateField($currencyRate);
        }

        return $this;
    }
}
