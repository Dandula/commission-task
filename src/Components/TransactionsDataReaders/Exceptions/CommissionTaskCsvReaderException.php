<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionsDataReaders\Exceptions;

use CommissionTask\Components\TransactionsDataReaders\Exceptions\Interfaces\CommissionTaskReaderException as CommissionTaskReaderExceptionContract;
use CommissionTask\Exceptions\CommissionTaskException;

final class CommissionTaskCsvReaderException extends CommissionTaskException implements CommissionTaskReaderExceptionContract { }
