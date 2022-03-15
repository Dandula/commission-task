<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Exceptions\CommissionTaskException;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;

abstract class Config
{
    private const CONFIG_NAME_SEPARATOR = '.';

    private static array $configSet;

    /**
     * Init configuration.
     *
     * @return void
     */
    public static function initConfig()
    {
        self::initEnvironments();
        self::initConfigSet();
    }

    /**
     * Get config value by name.
     */
    public static function getConfigByName(string $name): mixed
    {
        $configKeys = self::resolveConfigName($name);

        $configValue = self::$configSet;

        while ($configKeys && $configValue !== null) {
            $configKey = array_shift($configKeys);

            $configValue = $configValue[$configKey] ?? null;
        }

        return $configValue;
    }

    /**
     * Get config value by name.
     */
    public static function getEnvByName(string $name): string
    {
        return $_ENV[$name];
    }

    /**
     * Init global environments.
     */
    private static function initEnvironments(): void
    {
        $dotenv = new Dotenv();
        $dotenv->load(self::getEnvPath());
    }

    /**
     * Init global environments.
     */
    private static function initConfigSet(): void
    {
        self::$configSet = Yaml::parseFile(self::getConfigPath());
    }

    /**
     * Get full path to config file.
     */
    private static function getConfigPath(): string
    {
        $unresolvedPath = __DIR__
            .DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'
            .DIRECTORY_SEPARATOR.'config.yaml';

        return realpath($unresolvedPath);
    }

    /**
     * Get full path to .env file.
     */
    private static function getEnvPath(): string
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
     *
     * @throws CommissionTaskException
     */
    private static function resolveConfigName(string $name): array
    {
        if (!$name) {
            return [];
        }

        return explode(self::CONFIG_NAME_SEPARATOR, $name);
    }
}
