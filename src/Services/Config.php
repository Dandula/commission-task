<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;

class Config
{
    private const CONFIG_NAME_SEPARATOR = '.';

    private array $configSet;

    /**
     * Init configuration.
     */
    public function initConfig(): void
    {
        $this->initEnvironments();
        $this->initConfigSet();
    }

    /**
     * Get config set.
     */
    public function getConfigSet(): array
    {
        return $this->configSet;
    }

    /**
     * Get environment variables set.
     */
    public function getEnvSet(): array
    {
        return $_ENV;
    }

    /**
     * Get config value by name.
     */
    public function getConfigByName(string $name): mixed
    {
        $configKeys = $this->resolveConfigName($name);

        $configValue = $this->getConfigSet();

        while ($configKeys && $configValue !== null) {
            $configKey = array_shift($configKeys);

            $configValue = $configValue[$configKey] ?? null;
        }

        return $configValue;
    }

    /**
     * Get config value by name.
     */
    public function getEnvByName(string $name): string
    {
        $envSet = $this->getEnvSet();

        return $envSet[$name];
    }

    /**
     * Get transactions CSV column number.
     */
    public function getTransactionsCsvColumnNumber(string $column): int
    {
        return $this->getConfigByName('transactionsCsv.columnsNumbers.'.$column);
    }

    /**
     * Get acceptable currencies config.
     */
    public function getAcceptableCurrenciesConfig(): array
    {
        return $this->getConfigByName('currencies.acceptable');
    }

    /**
     * Get acceptable currencies codes.
     *
     * @return string[]
     */
    public function getAcceptableCurrenciesCodes(): array
    {
        $acceptableConfig = $this->getAcceptableCurrenciesConfig();

        return array_column($acceptableConfig, column_key: 'currencyCode');
    }

    /**
     * Get required currencies API fields.
     *
     * @return string[]
     */
    public function getRequiredCurrenciesApiFields(): array
    {
        return $this->getConfigByName('currenciesApi.requiredFields');
    }

    /**
     * Get currencies API field name.
     */
    public function getCurrenciesApiFieldName(string $name): string
    {
        return $this->getConfigByName('currenciesApi.requiredFields.'.$name);
    }

    /**
     * Init global environments.
     */
    private function initEnvironments(): void
    {
        $dotenv = new Dotenv();
        $dotenv->load($this->getEnvPath());
    }

    /**
     * Init global environments.
     */
    private function initConfigSet(): void
    {
        $this->configSet = Yaml::parseFile($this->getConfigPath());
    }

    /**
     * Get full path to config file.
     */
    private function getConfigPath(): string
    {
        $unresolvedPath = __DIR__
            .DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'
            .DIRECTORY_SEPARATOR.'config.yaml';

        return realpath($unresolvedPath);
    }

    /**
     * Get full path to .env file.
     */
    private function getEnvPath(): string
    {
        $unresolvedPath = __DIR__
            .DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'
            .DIRECTORY_SEPARATOR.'.env';

        return realpath($unresolvedPath);
    }

    /**
     * Resolve config name to array of keys in config set.
     *
     * @return string[]
     */
    private function resolveConfigName(string $name): array
    {
        if (!$name) {
            return [];
        }

        return explode(self::CONFIG_NAME_SEPARATOR, $name);
    }
}
