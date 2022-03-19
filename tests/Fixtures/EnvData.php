<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Fixtures;

class EnvData
{
    public static function getEnvData(): array
    {
        return [
            'CURRENCIES_API_URL' => 'https://api.example.com',
        ];
    }
}
