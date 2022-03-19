<?php

declare(strict_types=1);

namespace CommissionTask\Tests;

use CommissionTask\Components\CurrenciesDataReader\ApiCurrenciesDataReader;
use CommissionTask\Components\CurrenciesDataReader\Interfaces\CurrenciesDataReader as CurrenciesDataReaderContract;
use CommissionTask\Components\CurrenciesDataValidator\ApiCurrenciesDataValidator;
use CommissionTask\Components\CurrenciesDataValidator\Interfaces\CurrenciesDataValidator as CurrenciesDataValidatorContract;
use CommissionTask\Components\CurrenciesUpdater\ApiCurrenciesUpdater;
use CommissionTask\Components\CurrenciesUpdater\Interfaces\CurrenciesUpdater as CurrenciesUpdaterContract;
use CommissionTask\Components\Outputter\ConsoleOutputter;
use CommissionTask\Components\Outputter\Interfaces\Outputter as OutputterContract;
use CommissionTask\Components\Storage\ArrayStorage;
use CommissionTask\Components\TransactionDataValidator\CsvTransactionDataValidator;
use CommissionTask\Components\TransactionDataValidator\Interfaces\TransactionDataValidator as TransactionDataValidatorContract;
use CommissionTask\Components\TransactionFeeCalculator\Interfaces\TransactionFeeCalculator as TransactionFeeCalculatorContract;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\DepositStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\WithdrawBusinessStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\WithdrawPrivateStrategy;
use CommissionTask\Components\TransactionFeeCalculator\TransactionFeeCalculator;
use CommissionTask\Components\TransactionFeeCalculator\TransactionFeeCalculatorStrategyResolver;
use CommissionTask\Components\TransactionSaver\CsvTransactionSaver;
use CommissionTask\Components\TransactionSaver\Interfaces\TransactionSaver as TransactionSaverContract;
use CommissionTask\Components\TransactionsDataReader\CsvTransactionsDataReader;
use CommissionTask\Components\TransactionsDataReader\Interfaces\TransactionsDataReader as TransactionsDataReaderContract;
use CommissionTask\Kernel\Application;
use CommissionTask\Kernel\Container;
use CommissionTask\Repositories\CurrenciesRepository;
use CommissionTask\Repositories\Interfaces\TransactionsRepository as TransactionsRepositoryContract;
use CommissionTask\Repositories\TransactionsRepository;
use CommissionTask\Services\CommandLine as CommandLineService;
use CommissionTask\Services\Config as ConfigService;
use CommissionTask\Services\Currency as CurrencyService;
use CommissionTask\Services\Date as DateService;
use CommissionTask\Services\Filesystem as FilesystemService;
use CommissionTask\Services\Math as MathService;
use CommissionTask\Tests\Fixtures\ApiCurrenciesData;
use CommissionTask\Tests\Fixtures\ConfigData;
use CommissionTask\Tests\Fixtures\CsvTransactionsData;
use CommissionTask\Tests\Fixtures\EnvData;
use CommissionTask\Tests\Fixtures\TransactionsFeesData;
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

        $commandLineService = new CommandLineService();
        $mathService = new MathService();
        $dateService = new DateService();

        $arrayStorage = new ArrayStorage();
        $transactionRepository = new TransactionsRepository($arrayStorage);
        $currenciesRepository = new CurrenciesRepository($arrayStorage);

        $apiCurrenciesDataValidator = new ApiCurrenciesDataValidator($mockConfigService, $dateService);
        $apiCurrenciesUpdater = new ApiCurrenciesUpdater($currenciesRepository, $mockConfigService, $mathService);
        $currencyService = new CurrencyService(
            $currenciesRepository,
            $mockApiCurrenciesDataReader,
            $apiCurrenciesDataValidator,
            $apiCurrenciesUpdater,
            $mockConfigService,
            $mathService
        );
        $depositStrategy = new DepositStrategy($mockConfigService, $currencyService, $mathService);
        $withdrawPrivateStrategy = new WithdrawPrivateStrategy(
            $mockConfigService,
            $currencyService,
            $mathService,
            $dateService,
            $transactionRepository
        );
        $withdrawBusinessStrategy = new WithdrawBusinessStrategy($mockConfigService, $currencyService, $mathService);
        $transactionFeeCalculatorStrategyResolver = new TransactionFeeCalculatorStrategyResolver(
            $depositStrategy,
            $withdrawPrivateStrategy,
            $withdrawBusinessStrategy
        );
        $transactionFeeCalculator = new TransactionFeeCalculator(
            $transactionFeeCalculatorStrategyResolver
        );

        $csvTransactionsDataReader = new CsvTransactionsDataReader($stubFilesystemService);
        $csvTransactionDataValidator = new CsvTransactionDataValidator(
            $mockConfigService,
            $dateService,
            $currencyService,
        );
        $transactionSaver = new CsvTransactionSaver(
            $transactionRepository,
            $mockConfigService,
            $dateService,
        );

        $consoleOutputter = new ConsoleOutputter();

        $stubContainer = $this->createStub(Container::class);

        $stubContainer->method('get')
            ->willReturnMap([
                [ConfigService::class, $mockConfigService],
                [CommandLineService::class, $commandLineService],
                [TransactionsRepositoryContract::class, $transactionRepository],
                [CurrenciesDataReaderContract::class, $mockApiCurrenciesDataReader],
                [CurrenciesDataValidatorContract::class, $apiCurrenciesDataValidator],
                [CurrenciesUpdaterContract::class, $apiCurrenciesUpdater],
                [TransactionFeeCalculatorContract::class, $transactionFeeCalculator],
                [TransactionsDataReaderContract::class, $csvTransactionsDataReader],
                [TransactionDataValidatorContract::class, $csvTransactionDataValidator],
                [TransactionSaverContract::class, $transactionSaver],
                [OutputterContract::class, $consoleOutputter],
            ]);

        $this->application = new Application($stubContainer);
    }

    /**
     * @param string[] $expectation
     *
     * @dataProvider dataProviderForRunTesting
     */
    public function testRun(string $filePath, array $expectation): void
    {
        $argv = ['script.php', $filePath];
        $this->application->run(argc: count($argv), argv: $argv);

        $this->expectOutputString(implode(PHP_EOL, $expectation).PHP_EOL);
    }

    public function dataProviderForRunTesting(): array
    {
        return [
            'run application with example data' => [
                '/absolute/path/input.csv',
                TransactionsFeesData::getCalculatedFees(),
            ],
        ];
    }
}
