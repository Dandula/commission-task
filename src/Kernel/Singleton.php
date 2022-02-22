<?php

declare(strict_types=1);

namespace CommissionTask\Kernel;

use CommissionTask\Exceptions\CommissionTaskKernelException;
use Exception;

class Singleton
{
    /**
     * @var array
     */
    private static $instances = [];

    /**
     * @return void
     */
    public function __construct() { }

    /**
     * @return void
     */
    public function __clone() { }

    /**
     * @return mixed
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new CommissionTaskKernelException('Cannot unserialize singleton');
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        $subclass = static::class;

        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }

        return self::$instances[$subclass];
    }
}