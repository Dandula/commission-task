<?php

declare(strict_types=1);

namespace CommissionTask\Components\Outputter;

use CommissionTask\Components\Outputter\Exceptions\ConsoleOutputterException;
use CommissionTask\Components\Outputter\Interfaces\Outputter;

class ConsoleOutputter implements Outputter
{
    public const STRING_TYPE = 'string';
    public const ARRAY_TYPE = 'array';

    public const LINE_SEPARATOR = PHP_EOL;

    /**
     * {@inheritDoc}
     */
    public function output(string|array ...$outputtingDataItems): void
    {
        foreach ($outputtingDataItems as $outputtingDataItem) {
            switch (gettype($outputtingDataItem)) {
                case self::STRING_TYPE:
                    echo $outputtingDataItem.self::LINE_SEPARATOR;
                    break;
                case self::ARRAY_TYPE:
                    $this->output(...$outputtingDataItem);
                    break;
                default:
                    throw new ConsoleOutputterException(ConsoleOutputterException::UNSUPPORTED_OUTPUT_DATA_MESSAGE);
            }
        }
    }
}
