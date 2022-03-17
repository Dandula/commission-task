<?php

declare(strict_types=1);

namespace CommissionTask\Components\Outputter;

use CommissionTask\Components\Outputter\Interfaces\Outputter;

class ConsoleOutputter implements Outputter
{
    private const STRING_TYPE = 'string';
    private const ARRAY_TYPE = 'array';

    private const LINE_SEPARATOR = PHP_EOL;

    /**
     * {@inheritDoc}
     */
    public function output(string|array ...$outputtingDataItems): void
    {
        foreach ($outputtingDataItems as $outputtingDataItem) {
            switch (gettype($outputtingDataItem)) {
                case self::STRING_TYPE:
                default:
                    echo $outputtingDataItem.self::LINE_SEPARATOR;
                    break;
                case self::ARRAY_TYPE:
                    $this->output(...$outputtingDataItem);
                    break;
            }
        }
    }
}
