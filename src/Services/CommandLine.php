<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Exceptions\CommissionTaskException;
use CommissionTask\Exceptions\CommissionTaskKernelException;

class CommandLine
{
    private int $argc;

    /**
     * @var string[]
     */
    private array $argv;

    /**
     * Init command line parameters.
     */
    public function initCommandLineParameters(int $argc, array $argv): void
    {
        $this->argc = $argc;
        $this->argv = $argv;
    }

    /**
     * Get command line parameters by number.
     *
     * @throws CommissionTaskKernelException|CommissionTaskException
     */
    public function getCommandLineParameterByNumber(int $number): string
    {
        $this->checkIsCommandLineApplication();

        if (!isset($this->argv[$number])) {
            throw new CommissionTaskException(sprintf(CommissionTaskException::COMMAND_LINE_PARAMETER_IS_NOT_SET_MESSAGE, $number));
        }

        return $this->argv[$number];
    }

    /**
     * Check if the application is running from the command line.
     *
     * @throws CommissionTaskKernelException
     */
    private function checkIsCommandLineApplication(): void
    {
        if (!isset($this->argc, $this->argv)) {
            throw new CommissionTaskKernelException(CommissionTaskKernelException::SCRIPT_IS_NOT_RUN_FROM_COMMAND_LINE_MESSAGE);
        }
    }
}
