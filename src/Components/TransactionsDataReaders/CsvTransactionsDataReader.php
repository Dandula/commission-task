<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionsDataReaders;

use CommissionTask\Components\TransactionsDataReaders\Interfaces\TransactionsDataReader as TransactionsDataReaderContract;
use CommissionTask\Components\TransactionsDataReaders\Traits\TransactionsReadProcess;
use CommissionTask\Exceptions\CommissionTaskException;
use CommissionTask\Services\Filesystem;

class CsvTransactionsDataReader implements TransactionsDataReaderContract
{
    use TransactionsReadProcess;

    /**
     * @var Filesystem
     */
    private $filesystemService;

    /**
     * @var string
     */
    private $filePath;

    /**
     * Create a new CSV data reader instance.
     */
    public function __construct()
    {
        $this->filesystemService = Filesystem::getInstance();
    }

    /**
     * Set path to CSV file.
     *
     * @return void
     */
    public function setFilePath(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @inheritDoc
     */
    public function readTransactionsRawData(): array
    {
        if (!isset($this->filePath)) {
            throw new CommissionTaskException('The path to the CSV file is not specified');
        }

        if (!$this->filesystemService->isFileExists($this->filePath)) {
            throw new CommissionTaskException("The CSV file '$this->filePath' does not exist");
        }

        return $this->filesystemService->readFile($this->filePath);
    }

    /**
     * @inheritDoc
     */
    public function prepareTransactionsData(array $transactionsRawData): array
    {
        $transactionsData = [];

        foreach ($transactionsRawData as $transactionRawData) {
            $transactionsData[] = str_getcsv($transactionRawData);
        }

        return $transactionsData;
    }
}