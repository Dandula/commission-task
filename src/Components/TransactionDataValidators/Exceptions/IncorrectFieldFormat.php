<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidators\Exceptions;

use CommissionTask\Components\TransactionDataValidators\Exceptions\Interfaces\CommissionTaskValidationException as CommissionTaskValidationExceptionContract;
use CommissionTask\Exceptions\CommissionTaskException;

final class IncorrectFieldFormat extends CommissionTaskException implements CommissionTaskValidationExceptionContract { }
