<?php

declare(strict_types=1);

namespace CommissionTask\Components\DataFormatter;

final class ApiCurrenciesDataFormatter
{
    const BASE_CURRENCY_CODE_FIELD = 'base';
    const DATE_FIELD = 'date';
    const RATES_FIELD = 'rates';

    const MAIN_FIELDS = [
        self::BASE_CURRENCY_CODE_FIELD,
        self::DATE_FIELD,
        self::RATES_FIELD,
    ];

    const DATE_FORMAT = 'Y-m-d';
}
