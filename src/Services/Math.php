<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Kernel\Singleton;

class Math extends Singleton
{
    private $scale;

    public function __construct(int $scale)
    {
        parent::__construct();

        $this->scale = $scale;
    }

    public function add(string $leftOperand, string $rightOperand): string
    {
        return bcadd($leftOperand, $rightOperand, $this->scale);
    }
}
