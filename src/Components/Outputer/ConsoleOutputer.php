<?php

declare(strict_types=1);

namespace CommissionTask\Components\Outputer;

use CommissionTask\Components\Outputer\Exceptions\ConsoleOutputerException;
use CommissionTask\Components\Outputer\Interfaces\Outputer;

class ConsoleOutputer implements Outputer
{
    const STRING_TYPE = 'string';
    const ARRAY_TYPE  = 'array';

    const LINE_SEPARATOR = PHP_EOL;

    /**
     * @inheritDoc
     */
    public function output(...$outputtingDataItems)
    {
        foreach ($outputtingDataItems as $outputtingDataItem) {
            switch (gettype($outputtingDataItem)) {
                case self::STRING_TYPE:
                    $outputtingString = $outputtingDataItem;
                    break;
                case self::ARRAY_TYPE:
                    $outputtingString = implode(self::LINE_SEPARATOR, $outputtingDataItem);
                    break;
                default:
                    throw new ConsoleOutputerException(
                        ConsoleOutputerException::UNSUPPORTED_OUTPUT_DATA_MESSAGE
                    );
            }

            echo $outputtingString . self::LINE_SEPARATOR;
        }
    }
}
