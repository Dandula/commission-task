<?php

namespace CommissionTask\Kernel;

use CommissionTask\Components\TransactionsDataReaders\Interfaces\TransactionsDataReader;
use CommissionTask\Services\CommandLine;
use CommissionTask\Services\Filesystem;

class Application
{
    const FILEPATH_PARAMETER_NUMBER = 1;

    /**
     * The base path for the application installation.
     */
    protected $basePath;

    /**
     * The service container for the application.
     */
    protected $container;

    /**
     * Create a new application instance.
     */
    public function __construct(string $basePath, Container $container)
    {
        $this->setContainer($container)
            ->setBasePath($basePath);
    }

    /**
     * Run the application.
     *
     * @return void
     */
    public function run()
    {
        $transactionsData = $this->readTransactionsData();

        $this->validateTransactionsData($transactionsData);

        $transactionsFees = [];

//        $transactionCommissionCalcualtor = $container->get('calc');
//
//        foreach ($transactionsData as $transaction) {
//            $transactionCommissionCalcualtor->p
//        }

        $this->output($transactionsFees);
    }

    /**
     * Read transactions data.
     */
    private function readTransactionsData(): array
    {
        $commandLineService = $this->container->get(CommandLine::class);
        $filePath = $commandLineService->getCommandLineParameterByNumber(self::FILEPATH_PARAMETER_NUMBER);

        $transactionsDataReader = $this->container->get(TransactionsDataReader::class);
        $transactionsDataReader->setFilePath($filePath);

        return $transactionsDataReader->readTransactionsData();
    }

    /**
     * Validate transactions data.
     *
     * @param string[] $basePath
     * @return void
     */
    private function validateTransactionsData(array $basePath)
    {
    }

    /**
     * Output results of execution.
     *
     * @param mixed $outputData
     * @return void
     */
    private function output($outputData)
    {
        if (is_array($outputData)) {
            foreach ($outputData as $outputItem) {
                echo $outputItem . PHP_EOL;
            }
        } else {
            echo $outputData . PHP_EOL;
        }
    }

    /**
     * Set the base path for the application.
     *
     * @param string $basePath
     * @return $this
     */
    private function setBasePath(string $basePath): Application
    {
        $this->basePath = rtrim($basePath, '\/');

        if ($fileSystemService = $this->container->get(Filesystem::class)) {
            $fileSystemService->setBasePath($this->basePath);
        }

        return $this;
    }

    /**
     * Set the service container for application.
     *
     * @return $this
     */
    private function setContainer(Container $container): Application
    {
        $this->container = $container;

        return $this;
    }
}
