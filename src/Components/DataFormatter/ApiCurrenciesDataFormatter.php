<?php

declare(strict_types=1);

namespace CommissionTask\Components\DataFormatter;

final class ApiCurrenciesDataFormatter
{
    public const BASE_CURRENCY_CODE_FIELD = 'base';
    public const DATE_FIELD = 'date';
    public const RATES_FIELD = 'rates';

    public const MAIN_FIELDS = [
        self::BASE_CURRENCY_CODE_FIELD,
        self::DATE_FIELD,
        self::RATES_FIELD,
    ];

    public const DATE_FORMAT = 'Y-m-d';
}
