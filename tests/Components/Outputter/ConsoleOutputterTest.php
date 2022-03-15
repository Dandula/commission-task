<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Components\Outputter;

use CommissionTask\Components\Outputter\ConsoleOutputter;
use CommissionTask\Components\Outputter\Exceptions\ConsoleOutputterException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @coversDefaultClass ConsoleOutputter
 */
final class ConsoleOutputterTest extends TestCase
{
    /**
     * @var ConsoleOutputter
     */
    private $consoleOutputter;

    protected function setUp(): void
    {
        $this->consoleOutputter = new ConsoleOutputter();
    }

    /**
     * @param string[] $outputtingDataItems
     * @param string $expectation
     *
     * @dataProvider dataProviderForOutputTesting
     */
    public function testOutput(array $outputtingDataItems, string $expectation)
    {
        $this->expectOutputString($expectation);

        call_user_func_array([$this->consoleOutputter, 'output'], $outputtingDataItems);
    }

    public function dataProviderForOutputTesting(): array
    {
        return [
            'print a string' => [
                ['abc'],
                'abc' . ConsoleOutputter::LINE_SEPARATOR
            ],
            'print a few strings' => [
                ['abc', 'def'],
                'abc' . ConsoleOutputter::LINE_SEPARATOR . 'def' . ConsoleOutputter::LINE_SEPARATOR
            ],
            'print an array of strings' => [
                [['abc', 'def']],
                'abc' . ConsoleOutputter::LINE_SEPARATOR . 'def' . ConsoleOutputter::LINE_SEPARATOR
            ],
            'print a few arrays of strings' => [
                [['abc', 'def'], ['ghi', 'jkl']],
                'abc' . ConsoleOutputter::LINE_SEPARATOR . 'def' . ConsoleOutputter::LINE_SEPARATOR
                . 'ghi' . ConsoleOutputter::LINE_SEPARATOR . 'jkl' . ConsoleOutputter::LINE_SEPARATOR
            ],
            'print an array of strings and strings' => [
                [['abc', 'def'], 'ghi', 'jkl'],
                'abc' . ConsoleOutputter::LINE_SEPARATOR . 'def' . ConsoleOutputter::LINE_SEPARATOR
                . 'ghi' . ConsoleOutputter::LINE_SEPARATOR . 'jkl' . ConsoleOutputter::LINE_SEPARATOR
            ],
            'print an array of strings with triple nesting (unsupported)' => [
                [[['abc', 'def']]],
                'abc' . ConsoleOutputter::LINE_SEPARATOR . 'def' . ConsoleOutputter::LINE_SEPARATOR
            ],
        ];
    }
}
