<?php

declare(strict_types=1);

namespace CommissionTask\Components\Outputer\Interfaces;

use CommissionTask\Components\Outputer\Exceptions\Interfaces\OutputerException;

interface Outputer
{
    /**
     * Output given data.
     *
     * @param string|string[] ...$outputtingDataItems
     *
     * @return void
     *
     * @throws OutputerException
     */
    public function output(...$outputtingDataItems);
}
