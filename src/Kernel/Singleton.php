<?php

declare(strict_types=1);

namespace CommissionTask\Kernel;

use CommissionTask\Exceptions\CommissionTaskKernelException;

abstract class Singleton
{
    private static array $instances = [];

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    /**
     * @throws CommissionTaskKernelException
     */
    public function __wakeup()
    {
        throw new CommissionTaskKernelException(CommissionTaskKernelException::CANNOT_DESERIALIZE_SINGLETON);
    }

    /**
     * Return specified singleton.
     */
    public static function getInstance(): static
    {
        $subclass = static::class;

        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }

        return self::$instances[$subclass];
    }
}
