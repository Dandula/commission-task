<?php

declare(strict_types=1);

namespace CommissionTask\Entities;

use CommissionTask\Exceptions\CommissionTaskException;

abstract class BaseEntity
{
    public function __set(string $name, mixed $value): void
    {
        $this->propertyAccessError($name);
    }

    public function __get(string $name): mixed
    {
        $this->propertyAccessError($name);
    }

    public function __isset(string $name): bool
    {
        $this->propertyAccessError($name);
    }

    /**
     * @throws CommissionTaskException
     */
    private function propertyAccessError(string $name): void
    {
        throw new CommissionTaskException(sprintf(CommissionTaskException::FORBIDDEN_PROPERTY_ACCESS_MESSAGE, $name));
    }
}
