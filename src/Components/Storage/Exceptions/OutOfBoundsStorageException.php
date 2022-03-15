<?php

declare(strict_types=1);

namespace CommissionTask\Components\Storage\Exceptions;

use CommissionTask\Exceptions\CommissionTaskOutOfBoundsException;

final class OutOfBoundsStorageException extends CommissionTaskOutOfBoundsException
{
    public const DATA_ITEM_ID_DOESNT_EXISTS_MESSAGE = "The data item with the ID %s at part '%s' doesn't exist";
}
