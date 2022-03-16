<?php

declare(strict_types=1);

namespace CommissionTask\Components\Outputter\Interfaces;

interface Outputter
{
    /**
     * Output given data.
     *
     * @param string|string[] ...$outputtingDataItems
     */
    public function output(string|array ...$outputtingDataItems): void;
}
