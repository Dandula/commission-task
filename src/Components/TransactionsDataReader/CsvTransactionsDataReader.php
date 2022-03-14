<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionsDataReader;

use CommissionTask\Components\TransactionsDataReader\Exceptions\CsvTransactionsDataReaderException;
use CommissionTask\Components\TransactionsDataReader\Interfaces\TransactionsDataReader as TransactionsDataReaderContract;
use CommissionTask\Services\Filesystem as FilesystemService;

class CsvTransactionsDataReader implements TransactionsDataReaderContract
{
    /**
     * @var FilesystemService
     */
    private $filesystemService;

    /**
     * @var string
     */
    private $filePath;

    /**
     * Create a new CSV transactions data reader instance.
     */
    public function __construct(FilesystemService $filesystem)
    {
        $this->filesystemService = $filesystem;
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
     * {@inheritDoc}
     */
    public function readTransactionsData(): array
    {
        $transactionsRawData = $this->readCsvStrings();

        return $this->parseCsvStrings($transactionsRawData);
    }

    /**
     * Read CSV strings.
     *
     * @return string[]
     *
     * @throws CsvTransactionsDataReaderException
     */
    private function readCsvStrings(): array
    {
        if (!isset($this->filePath)) {
            throw new CsvTransactionsDataReaderException(CsvTransactionsDataReaderException::UNDEFINED_CSV_FILEPATH_MESSAGE);
        }

        if (!$this->filesystemService->isFileExists($this->filePath)) {
            throw new CsvTransactionsDataReaderException(sprintf(CsvTransactionsDataReaderException::CSV_FILE_DOESNT_EXISTS_MESSAGE, $this->filePath));
        }

        return $this->filesystemService->readFile($this->filePath);
    }

    /**
     * Parse CSV strings.
     *
     * @param string[] $transactionsRawData
     */
    private function parseCsvStrings(array $transactionsRawData): array
    {
        foreach ($transactionsRawData as &$transactionRawData) {
            $transactionRawData = str_getcsv($transactionRawData);
        }

        return $transactionsRawData;
    }
}