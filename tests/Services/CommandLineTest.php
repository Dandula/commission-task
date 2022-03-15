<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Services;

use CommissionTask\Exceptions\CommissionTaskException;
use CommissionTask\Exceptions\CommissionTaskKernelException;
use CommissionTask\Services\CommandLine as CommandLineService;
use PHPUnit\Framework\TestCase;

/**
 * @backupGlobals      enabled
 * @coversDefaultClass CommandLineService
 */
final class CommandLineTest extends TestCase
{
    /**
     * @var CommandLineService
     */
    private $commandLineService;

    protected function setUp(): void
    {
        $this->commandLineService = new CommandLineService();
    }

    /**
     * @param int $number
     * @param string[] $argv
     * @param string $expectation
     *
     * @dataProvider dataProviderForGetCommandLineParameterByNumberTesting
     */
    public function testGetCommandLineParameterByNumber(int $number, array $argv, string $expectation)
    {
        $_SERVER['argc'] = count($argv);
        $_SERVER['argv'] = $argv;

        $this->assertEquals(
            $expectation,
            $this->commandLineService->getCommandLineParameterByNumber($number)
        );
    }

    /**
     * @param int $number
     * @param string[] $argv
     * @param string $expectException
     * @param string $expectExceptionMessage
     *
     * @dataProvider dataProviderForGetCommandLineParameterByNumberIsNotSetTesting
     */
    public function testGetCommandLineParameterByNumberIsNotSet(
        int $number,
        array $argv,
        string $expectException,
        string $expectExceptionMessage
    )
    {
        $_SERVER['argc'] = count($argv);
        $_SERVER['argv'] = $argv;

        $this->expectException($expectException);
        $this->expectExceptionMessage(sprintf($expectExceptionMessage, $number));

        $this->commandLineService->getCommandLineParameterByNumber($number);
    }

    /**
     * @param int $number
     * @param string $expectException
     * @param string $expectExceptionMessage
     *
     * @dataProvider dataProviderForGetCommandLineParameterByNumberFailureTesting
     */
    public function testGetCommandLineParameterByNumberFailure(
        int $number,
        string $expectException,
        string $expectExceptionMessage
    )
    {
        unset($_SERVER['argc']);
        unset($_SERVER['argv']);

        $this->expectException($expectException);
        $this->expectExceptionMessage($expectExceptionMessage);

        $this->commandLineService->getCommandLineParameterByNumber($number);
    }

    public function dataProviderForGetCommandLineParameterByNumberTesting(): array
    {
        return [
            'get first parameter' => [0, ['-f', 'input'], '-f'],
            'get second parameter' => [1, ['-f', '--option'], '--option'],
            'get third parameter' => [2, ['-f', '--option', 'input'], 'input'],
        ];
    }

    public function dataProviderForGetCommandLineParameterByNumberIsNotSetTesting(): array
    {
        return [
            'get parameter which not set' => [
                2,
                ['-f', 'input'],
                CommissionTaskException::class,
                CommissionTaskException::COMMAND_LINE_PARAMETER_IS_NOT_SET_MESSAGE
            ],
            'get parameter when parameters is empty' => [
                0,
                [],
                CommissionTaskException::class,
                CommissionTaskException::COMMAND_LINE_PARAMETER_IS_NOT_SET_MESSAGE
            ],
        ];
    }

    public function dataProviderForGetCommandLineParameterByNumberFailureTesting(): array
    {
        return [
            'get parameter which not set' => [
                0,
                CommissionTaskKernelException::class,
                CommissionTaskKernelException::SCRIPT_IS_NOT_RUN_FROM_COMMAND_LINE_MESSAGE
            ],
        ];
    }
}
