<?php

declare(strict_types=1);

namespace CommissionTask\Components\DataFormatter;

final class CsvTransactionDataFormatter
{
    const COLUMNS_NUMBER = 6;

    const COLUMN_DATE_NUMBER = 0;
    const COLUMN_USER_ID_NUMBER = 1;
    const COLUMN_USER_TYPE_NUMBER = 2;
    const COLUMN_TYPE_NUMBER = 3;
    const COLUMN_AMOUNT_NUMBER = 4;
    const COLUMN_CURRENCY_CODE_NUMBER = 5;

    const COLUMN_DATE_FORMAT = 'Y-m-d';
}
