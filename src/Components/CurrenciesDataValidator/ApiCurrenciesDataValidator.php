<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidator;

use CommissionTask\Components\CurrenciesDataValidator\Exceptions\CurrenciesDataValidatorException;
use CommissionTask\Components\CurrenciesDataValidator\Interfaces\CurrenciesDataValidator as CurrenciesDataValidatorsContract;
use CommissionTask\Components\CurrenciesDataValidator\Traits\FieldFormat;
use CommissionTask\Services\Config as ConfigService;
use CommissionTask\Services\Date as DateService;

class ApiCurrenciesDataValidator implements CurrenciesDataValidatorsContract
{
    use FieldFormat;

    private array $validatedData;

    /**
     * Create CSV transaction data validator instance.
     */
    public function __construct(
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
     * @throws CurrenciesDataValidatorException|CurrenciesDataValidatorException
     */
    private function validateMainFields(): self
    {
        return $this->validateMainFieldsExistence()
            ->validateBaseCurrencyCodeMainField()
            ->validateRatesMainField();
    }

    /**
     * Validate main fields existence.
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateMainFieldsExistence(): self
    {
        if (array_diff(
            $this->getRequiredCurrenciesApiFields(),
            array_keys($this->validatedData)
        )) {
            throw new CurrenciesDataValidatorException(CurrenciesDataValidatorException::NO_REQUIRED_FIELDS_MESSAGE);
        }

        return $this;
    }

    /**
     * Validate base currency code at main fields.
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateBaseCurrencyCodeMainField(): self
    {
        $baseCurrencyCodeField = $this->getCurrenciesApiFieldName('baseCurrencyCode');

        return $this->validateCurrencyCodeField($this->validatedData[$baseCurrencyCodeField]);
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
        $ratesField = $this->getCurrenciesApiFieldName('rates');

        return $this->validateIsArrayField($this->validatedData[$ratesField]);
    }

    /**
     * Validate currencies rates fields.
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException|CurrenciesDataValidatorException
     */
    private function validateCurrenciesRates(): self
    {
        $ratesField = $this->getCurrenciesApiFieldName('rates');
        $currenciesRatesData = $this->validatedData[$ratesField];

        foreach ($currenciesRatesData as $currencyCode => $currencyRate) {
            $this->validateCurrencyCodeField($currencyCode)
                ->validateCurrencyRateField($currencyRate);
        }

        return $this;
    }

    /**
     * Get required currencies API fields.
     *
     * @return string[]
     */
    private function getRequiredCurrenciesApiFields(): array
    {
        return ConfigService::getConfigByName('currenciesApi.requiredFields');
    }

    /**
     * Get currencies API field name.
     */
    private function getCurrenciesApiFieldName(string $name): string
    {
        return ConfigService::getConfigByName('currenciesApi.requiredFields.'.$name);
    }
}
