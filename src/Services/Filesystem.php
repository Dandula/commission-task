<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Kernel\Singleton;

class Filesystem extends Singleton
{
    private const MAX_LENGTH_LINE_CSV = 128;

    /**
     * Check is file exists.
     */
    public function isFileExists(string $filePath): bool
    {
        return file_exists($filePath);
    }

    /**
     * Open file.
     *
     * @return false|resource
     */
    public function openFile(string $filePath)
    {
        return fopen($filePath, mode: 'rb');
    }

    /**
     * Read CSV file line by line.
     *
     * @param resource $fileResource
     */
    public function readCsvRows($fileResource): iterable
    {
        while (
            !feof($fileResource)
            && $buffer = fgetcsv($fileResource, length: self::MAX_LENGTH_LINE_CSV)
        ) {
            yield $buffer;
        }
    }

    /**
     * Close file.
     *
     * @param resource $fileResource
     */
    public function closeFile($fileResource): void
    {
        fclose($fileResource);
    }
}
