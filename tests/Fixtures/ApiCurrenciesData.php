<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Fixtures;

class ApiCurrenciesData
{
    public static function getApiCurrenciesData(): array
    {
        return [
            'base' => 'EUR',
            'date' => '2022-02-24',
            'rates' => [
                'EUR' => 1,
                'JPY' => 129.53,
                'USD' => 1.1497,
            ],
        ];
    }
}
