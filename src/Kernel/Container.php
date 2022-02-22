<?php

namespace CommissionTask\Kernel;

use CommissionTask\Components\TransactionsDataReaders\CsvTransactionsDataReader;
use CommissionTask\Components\TransactionsDataReaders\Interfaces\TransactionsDataReader;
use CommissionTask\Exceptions\CommissionTaskKernelException;
use CommissionTask\Services\CommandLine;
use CommissionTask\Services\Date;
use CommissionTask\Services\Filesystem;

class Container
{
    private $instances = [];

    /**
     * Init service container.
     *
     * @return void
     * @throws CommissionTaskKernelException
     */
    public function init()
    {
        // Put singletons
        $this->put(Filesystem::class, Filesystem::getInstance());

        // Put classes instances
        $this->put(CommandLine::class, new CommandLine());
        $this->put(Date::class, new Date());

        // Put implemented classes instances
        $this->put(TransactionsDataReader::class, new CsvTransactionsDataReader(
            $this->get(Filesystem::class)
        ));
    }

    /**
     * Get service from service container.
     *
     * @return mixed
     * @throws CommissionTaskKernelException
     */
    public function get(string $alias)
    {
        if (!isset($this->instances[$alias])) {
            throw new CommissionTaskKernelException("Undefined service $alias");
        }

        return $this->instances[$alias];
    }

    /**
     * Put service to service container.
     *
     * @return void
     */
    public function put(string $alias, $instance)
    {
        $this->instances[$alias] = $instance;
    }
}
