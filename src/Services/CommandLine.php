<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Exceptions\CommissionTaskException;
use CommissionTask\Exceptions\CommissionTaskKernelException;

class CommandLine
{
    /**
     * Get command line parameters by number.
     *
     * @throws CommissionTaskKernelException|CommissionTaskException
     */
    public function getCommandLineParameterByNumber(int $number): string
    {
        $this->checkIsCommandLineApplication();

        $commandLineParameters = $_SERVER['argv'];

        if (!isset($commandLineParameters[$number])) {
            throw new CommissionTaskException("The command line parameter #$number is not set");
        }

        return $commandLineParameters[$number];
    }

    /**
     * Check if the application is running from the command line..
     *
     * @return void
     * @throws CommissionTaskKernelException
     */
    private function checkIsCommandLineApplication()
    {
        if (!isset($_SERVER['argv']) || !isset($_SERVER['argc'])) {
            throw new CommissionTaskKernelException('The script is not run from the command line');
        }
    }
}
