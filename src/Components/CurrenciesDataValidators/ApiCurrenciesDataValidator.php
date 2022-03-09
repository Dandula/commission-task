<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidators;

use CommissionTask\Components\CurrenciesDataValidators\Traits\FieldFormat;
use CommissionTask\Components\DataFormatters\ApiCurrenciesDataFormatter;
use CommissionTask\Components\CurrenciesDataValidators\Exceptions\ApiCurrenciesDataValidatorException;
use CommissionTask\Components\CurrenciesDataValidators\Exceptions\CurrenciesDataValidatorException;
use CommissionTask\Components\CurrenciesDataValidators\Interfaces\CurrenciesDataValidator as CurrenciesDataValidatorsContract;
use CommissionTask\Services\Date;

class ApiCurrenciesDataValidator implements CurrenciesDataValidatorsContract
{
    use FieldFormat;

    /**
     * @var ApiCurrenciesDataFormatter
     */
    private $apiCurrenciesDataFormatter;

    /**
     * @var Date
     */
    private $dateService;

    /**
     * @var array
     */
    private $validatedData;

    /**
     * Create CSV transaction data validator instance.
     */
    public function __construct(ApiCurrenciesDataFormatter $apiCurrenciesDataFormatter, Date $dateService)
    {
        $this->apiCurrenciesDataFormatter = $apiCurrenciesDataFormatter;
        $this->dateService = $dateService;
    }

    /**
     * @inheritDoc
     */
    public function validateCurrenciesData(array $currenciesData)
    {
        $this->validatedData = $currenciesData;

        $this->validateMainFields()
            ->validateCurrenciesRates();
    }

    /**
     * Validate main fields.
     *
     * @return $this
     * @throws CurrenciesDataValidatorException|ApiCurrenciesDataValidatorException
     */
    private function validateMainFields(): ApiCurrenciesDataValidator
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
     * @throws ApiCurrenciesDataValidatorException
     */
    private function validateMainFieldsExistence(): ApiCurrenciesDataValidator
    {
        if (array_diff(
                $this->apiCurrenciesDataFormatter::MAIN_FIELDS,
                array_keys($this->validatedData)
            )) {
            throw new ApiCurrenciesDataValidatorException(
                ApiCurrenciesDataValidatorException::NO_REQUIRED_FIELDS_MESSAGE
            );
        }

        return $this;
    }

    /**
     * Validate base currency code at main fields.
     *
     * @return $this
     * @throws ApiCurrenciesDataValidatorException
     */
    private function validateBaseCurrencyCodeMainField(): ApiCurrenciesDataValidator
    {
        return $this->validateCurrencyCodeField(
            $this->validatedData[$this->apiCurrenciesDataFormatter::BASE_CURRENCY_CODE_FIELD]
        );
    }

    /**
     * Validate date at main fields.
     *
     * @return $this
     * @throws CurrenciesDataValidatorException
     */
    private function validateDateMainField(): ApiCurrenciesDataValidator
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
     * @throws CurrenciesDataValidatorException
     */
    private function validateRatesMainField(): ApiCurrenciesDataValidator
    {
        return $this->validateIsArrayField($this->validatedData[$this->apiCurrenciesDataFormatter::RATES_FIELD]);
    }

    /**
     * Validate currencies rates fields.
     *
     * @return $this
     * @throws CurrenciesDataValidatorException|ApiCurrenciesDataValidatorException
     */
    private function validateCurrenciesRates(): ApiCurrenciesDataValidator
    {
        $currenciesRatesData = $this->validatedData[$this->apiCurrenciesDataFormatter::RATES_FIELD];

        foreach ($currenciesRatesData as $currencyCode => $currencyRate) {
            $this->validateCurrencyCodeField($currencyCode)
                ->validateCurrencyRateField($currencyRate);
        }

        return $this;
    }
}
