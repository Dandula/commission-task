<?php

declare(strict_types=1);

namespace CommissionTask\Components\Outputter\Interfaces;

use Stringable;

interface Outputter
{
    /**
     * Output given data.
     */
    public function output(string|Stringable|array ...$outputtingDataItems): void;
}
