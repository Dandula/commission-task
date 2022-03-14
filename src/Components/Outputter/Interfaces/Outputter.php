<?php

declare(strict_types=1);

namespace CommissionTask\Components\Outputter\Interfaces;

use CommissionTask\Components\Outputter\Exceptions\Interfaces\OutputterException;

interface Outputter
{
    /**
     * Output given data.
     *
     * @param string|string[] ...$outputtingDataItems
     *
     * @return void
     *
     * @throws OutputterException
     */
    public function output(...$outputtingDataItems);
}