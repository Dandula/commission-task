<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Kernel\Singleton;

class Filesystem extends Singleton
{
    /**
     * Check is file exists.
     *
     * @param string $filePath
     * @return bool
     */
    public function isFileExists(string $filePath): bool
    {
        return file_exists($filePath);
    }

    /**
     * Read file line by line to array.
     *
     * @param string $filePath
     * @return string[]
     */
    public function readFile(string $filePath): array
    {
        $fp = fopen($filePath,'r');

        try {
            $content = [];
            while (!feof($fp) && ($buffer = fgets($fp)) !== false) {
                $content[] = rtrim($buffer);
            }
        } finally {
            fclose($fp);
        }

        return $content;
    }
}
