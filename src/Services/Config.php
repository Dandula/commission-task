<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Exceptions\CommissionTaskException;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;

abstract class Config
{
    const CONFIG_NAME_SEPARATOR = '.';

    /**
     * @var array
     */
    public static $configSet;

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
     *
     * @return mixed
     */
    public static function getConfigByName(string $name)
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
        return getenv($name);
    }

    /**
     * Init global environments.
     *
     * @return void
     */
    private static function initEnvironments()
    {
        $dotenv = new Dotenv();
        $dotenv->load(self::getEnvPath());
    }

    /**
     * Init global environments.
     *
     * @return void
     */
    private static function initConfigSet()
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

        $configKeys = explode(self::CONFIG_NAME_SEPARATOR, $name);

        if ($configKeys === false) {
            throw new CommissionTaskException(sprintf(CommissionTaskException::UNRESOLVED_CONFIG_NAME, $name));
        }

        return $configKeys;
    }
}
