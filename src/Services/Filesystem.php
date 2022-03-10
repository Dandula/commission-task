<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Kernel\Singleton;

class Filesystem extends Singleton
{
    /**
     * @var string
     */
    private $basePath;

    /**
     * Set the base path for the filesystem service.
     *
     * @return void
     */
    public function setBasePath(string $basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Check is file exists.
     */
    public function isFileExists(string $filePath): bool
    {
        $filePath = $this->resolvePath($filePath);

        return (bool) $filePath;
    }

    /**
     * Read file line by line to array.
     *
     * @return string[]
     */
    public function readFile(string $filePath): array
    {
        $filePath = $this->resolvePath($filePath);

        $fp = fopen($filePath, 'r');

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

    /**
     * Resolve relative path to absolute.
     *
     * @return false|string
     */
    private function resolvePath(string $path)
    {
        if ($this->basePath) {
            $path = $this->basePath.DIRECTORY_SEPARATOR.$path;
        }

        return realpath($path);
    }
}
