<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Kernel\Singleton;

class Filesystem extends Singleton
{
    /**
     * Check is file exists.
     */
    public function isFileExists(string $filePath): bool
    {
        return file_exists($filePath);
    }

    /**
     * Read file line by line to array.
     *
     * @return string[]
     */
    public function readFile(string $filePath): array
    {
        $fileResource = fopen($filePath, 'rb');

        try {
            $content = [];
            while (!feof($fileResource) && ($buffer = fgets($fileResource)) !== false) {
                $content[] = rtrim($buffer);
            }
        } finally {
            fclose($fileResource);
        }

        return $content;
    }
}
