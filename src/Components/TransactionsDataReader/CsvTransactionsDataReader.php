<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionsDataReader;

use CommissionTask\Components\TransactionsDataReader\Exceptions\TransactionsDataReaderException;
use CommissionTask\Components\TransactionsDataReader\Interfaces\TransactionsDataReader as TransactionsDataReaderContract;
use CommissionTask\Services\Filesystem as FilesystemService;

class CsvTransactionsDataReader implements TransactionsDataReaderContract
{
    /**
     * @var resource
     */
    private $fileResource;

    /**
     * Create a new CSV transactions data reader instance.
     */
    public function __construct(
        private FilesystemService $filesystemService
    ) {
    }

    /**
     * Destroy current CSV transactions data reader instance.
     */
    public function __destruct()
    {
        if ($this->fileResource !== null) {
            $this->filesystemService->closeFile($this->fileResource);
        }
    }

    /**
     * Open CSV file.
     */
    public function openFile(string $filePath): void
    {
        if (!$this->filesystemService->isFileExists($filePath)) {
            throw new TransactionsDataReaderException(sprintf(TransactionsDataReaderException::CSV_FILE_DOESNT_EXISTS_MESSAGE, $filePath));
        }

        $this->fileResource = $this->filesystemService->openFile($filePath);
    }

    /**
     * {@inheritDoc}
     */
    public function readTransactionsData(): iterable
    {
        yield from $this->filesystemService->readCsvRows($this->fileResource);
    }
}
