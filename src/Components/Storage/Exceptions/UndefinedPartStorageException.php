<?php

declare(strict_types=1);

namespace CommissionTask\Components\Storage\Exceptions;

use CommissionTask\Components\Storage\Exceptions\Interfaces\StorageException as StorageExceptionContract;
use CommissionTask\Exceptions\CommissionTaskException;

final class UndefinedPartStorageException extends CommissionTaskException implements StorageExceptionContract
{
    const UNDEFINED_REPOSITORY_PART_MESSAGE = "Undefined part '%s' of repository";
}
