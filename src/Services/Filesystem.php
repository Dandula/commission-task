<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Kernel\Singleton;

class Filesystem extends Singleton
{
    private string $basePath;

    /**
     * Set the base path for the filesystem service.
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * Check is file exists.
     */
    public function isFileExists(string $filePath): bool
    {
        $resolvedFilePath = $this->resolvePath($filePath);

        return (bool) $resolvedFilePath;
    }

    /**
     * Read file line by line to array.
     *
     * @return string[]
     */
    public function readFile(string $filePath): array
    {
        $resolvedFilePath = $this->resolvePath($filePath);

        $fileResource = fopen($resolvedFilePath, 'rb');

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

    /**
     * Resolve relative path to absolute.
     */
    private function resolvePath(string $path): false|string
    {
        if ($this->basePath) {
            $path = $this->basePath.DIRECTORY_SEPARATOR.$path;
        }

        return realpath($path);
    }
}
