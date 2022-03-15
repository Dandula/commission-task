<?php

declare(strict_types=1);

namespace CommissionTask\Components\DataFormatter;

final class CsvTransactionDataFormatter
{
    public const COLUMNS_NUMBER = 6;

    public const COLUMN_DATE_NUMBER = 0;
    public const COLUMN_USER_ID_NUMBER = 1;
    public const COLUMN_USER_TYPE_NUMBER = 2;
    public const COLUMN_TYPE_NUMBER = 3;
    public const COLUMN_AMOUNT_NUMBER = 4;
    public const COLUMN_CURRENCY_CODE_NUMBER = 5;

    public const COLUMN_DATE_FORMAT = 'Y-m-d';
}
