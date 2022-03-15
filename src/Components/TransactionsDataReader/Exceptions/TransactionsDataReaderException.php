<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionsDataReader\Exceptions;

use CommissionTask\Exceptions\CommissionTaskException;

final class TransactionsDataReaderException extends CommissionTaskException
{
    public const UNDEFINED_CSV_FILEPATH_MESSAGE = 'The path to the CSV file is not specified';
    public const CSV_FILE_DOESNT_EXISTS_MESSAGE = "The CSV file %s doesn't exist";
}
