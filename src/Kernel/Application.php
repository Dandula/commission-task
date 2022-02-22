<?php

namespace CommissionTask\Kernel;

use CommissionTask\Components\TransactionsDataReaders\CsvTransactionsDataReader;
use CommissionTask\Services\CommandLine;

class Application
{
    /**
     * The base path for the application installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * The service of command line.
     *
     * @var CommandLine
     */
    protected $commandLineService;

    /**
     * Create a new application instance.
     *
     * @param string|null $basePath
     */
    public function __construct(string $basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }

        $this->commandLineService = CommandLine::getInstance();
    }

    /**
     * Set the base path for the application.
     *
     * @param string $basePath
     * @return $this
     */
    public function setBasePath(string $basePath): Application
    {
        $this->basePath = rtrim($basePath, '\/');

        return $this;
    }

    /**
     * Read transactions data.
     *
     * @return array
     */
    public function readTransactionsData(): array
    {
        $filePath = realpath($this->basePath . DIRECTORY_SEPARATOR
            . $this->commandLineService->getCommandLineParameterByNumber(1));

        $transactionDataReader = new CsvTransactionsDataReader();
        $transactionDataReader->setFilePath($filePath);

        return $transactionDataReader->readTransactionsData();
    }

    /**
     * Validate transactions data.
     *
     * @param string[] $basePath
     * @return void
     */
    public function validateTransactionsData(array $basePath)
    {
    }

    /**
     * Run the application.
     *
     * @param array $transactionsData
     * @return array
     */
    public function run(array $transactionsData): array
    {
        return [];
    }

    /**
     * Output results of execution.
     *
     * @param mixed $outputData
     * @return void
     */
    public function output($outputData)
    {
        if (is_array($outputData)) {
            foreach ($outputData as $outputItem) {
                echo $outputItem . PHP_EOL;
            }
        } else {
            echo $outputData . PHP_EOL;
        }
    }
}