<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Exceptions\CommissionTaskException;
use CommissionTask\Exceptions\CommissionTaskKernelException;
use CommissionTask\Kernel\Singleton;

class CommandLine extends Singleton
{
    public function __construct()
    {
        parent::__construct();

        if (!isset($_SERVER['argv']) || !isset($_SERVER['argc'])) {
            throw new CommissionTaskKernelException('The script is not run from the command line');
        }
    }

    /**
     * Get command line parameters.
     *
     * @return array
     */
    public function getCommandLineParameters(): array
    {
        $commandLineParameters = $_SERVER['argv'];
        $commandLineParametersCount = $_SERVER['argc'];

        if ($commandLineParametersCount === 0) {
            return [];
        }

        return array_shift($commandLineParameters);
    }

    /**
     * Get command line parameters by number.
     *
     * @param int $number
     * @return string
     * @throws CommissionTaskException
     */
    public function getCommandLineParameterByNumber(int $number): string
    {
        $commandLineParameters = $_SERVER['argv'];

        if (!isset($commandLineParameters[$number])) {
            throw new CommissionTaskException("The command line parameter #$number is not set");
        }

        return $commandLineParameters[$number];
    }
}
