<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Fixtures;

class ConfigData
{
    public static function getConfigData(): array
    {
        return [
            'transactionsCsv' => [
                'columnsNumbers' => [
                    'date' => 0,
                    'userId' => 1,
                    'userType' => 2,
                    'type' => 3,
                    'amount' => 4,
                    'currencyCode' => 5,
                ],
                'dateFormat' => 'Y-m-d',
            ],
            'currencies' => [
                'baseCurrency' => 'EUR',
                'rateScale' => 6,
                'acceptable' => [
                    [
                        'currencyCode' => 'EUR',
                        'scale' => 2,
                    ],
                    [
                        'currencyCode' => 'USD',
                        'scale' => 2,
                    ],
                    [
                        'currencyCode' => 'JPY',
                        'scale' => 0,
                    ],
                ],
            ],
            'currenciesApi' => [
                'maxRequestsAttempts' => 3,
                'requiredFields' => [
                    'baseCurrencyCode' => 'base',
                    'rates' => 'rates',
                ],
            ],
            'feeCalculator' => [
                'roundedOffDigitsNumber' => 1,
                'feeRateDeposit' => '0.0003',
                'feeRateWithdrawPrivate' => '0.003',
                'feeRateWithdrawBusiness' => '0.005',
                'nontaxableAmountWithdrawPrivate' => '1000.00',
                'nontaxableCurrencyCodeWithdrawPrivate' => 'EUR',
                'preferentialOperationsNumberWithdrawPrivate' => 3,
            ],
        ];
    }
}
