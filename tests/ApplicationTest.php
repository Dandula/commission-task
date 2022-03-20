<?php

declare(strict_types=1);

namespace CommissionTask\Tests;

use CommissionTask\Components\CurrenciesDataReader\ApiCurrenciesDataReader;
use CommissionTask\Kernel\Application;
use CommissionTask\Kernel\Container;
use CommissionTask\Services\Config as ConfigService;
use CommissionTask\Services\Filesystem as FilesystemService;
use CommissionTask\Tests\Fixtures\ApiCurrenciesData;
use CommissionTask\Tests\Fixtures\ConfigData;
use CommissionTask\Tests\Fixtures\CsvTransactionsData;
use CommissionTask\Tests\Fixtures\EnvData;
use PHPUnit\Framework\TestCase;

/**
 * @backupGlobals enabled
 */
final class ApplicationTest extends TestCase
{
    private Application $application;

    protected function setUp(): void
    {
        $stubFilesystemService = $this->createStub(FilesystemService::class);

        $stubFilesystemService->method('isFileExists')
            ->willReturn(true);

        $stubFilesystemService->method('readCsvRows')
            ->willReturnCallback(fn () => yield from CsvTransactionsData::getCsvRows());

        $mockConfigService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getEnvSet', 'getConfigSet'])
            ->getMock();

        $mockConfigService->method('getEnvSet')
            ->willReturn(EnvData::getEnvData());

        $mockConfigService->method('getConfigSet')
            ->willReturn(ConfigData::getConfigData());

        $mockApiCurrenciesDataReader = $this->getMockBuilder(ApiCurrenciesDataReader::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['readCurrenciesData'])
            ->getMock();

        $mockApiCurrenciesDataReader->method('readCurrenciesData')
            ->willReturn(ApiCurrenciesData::getApiCurrenciesData());

        $container = new Container();

        $container->put('filesystemService', $stubFilesystemService);
        $container->put('configService', $mockConfigService);
        $container->put('currenciesDataReader', $mockApiCurrenciesDataReader);

        $container->init();

        $this->application = new Application($container);
    }

    /**
     * @dataProvider dataProviderForRunTesting
     */
    public function testRun(string $filePath, string $expectation): void
    {
        $argv = ['script.php', $filePath];
        $this->application->run(argc: count($argv), argv: $argv);

        $this->expectOutputString($expectation);
    }

    public function dataProviderForRunTesting(): array
    {
        $calculatedTransactionsFees = [
            '0.60',
            '3.00',
            '0.00',
            '0.06',
            '1.50',
            '0',
            '0.70',
            '0.30',
            '0.30',
            '3.00',
            '0.00',
            '0.00',
            '8612',
        ];

        return [
            'run application with example data' => [
                '/absolute/path/input.csv',
                implode(PHP_EOL, $calculatedTransactionsFees).PHP_EOL,
            ],
        ];
    }
}
