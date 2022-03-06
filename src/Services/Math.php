<?php

declare(strict_types=1);

namespace CommissionTask\Services;

class Math
{
    const NUMBER_SYSTEM_BASE = '10';

    /**
     * @var int
     */
    private $scale;

    public function __construct(int $scale)
    {
        $this->scale = $scale;
    }

    public function add(string $leftOperand, string $rightOperand): string
    {
        return bcadd($leftOperand, $rightOperand, $this->scale);
    }

    public function mul(string $leftOperand, string $rightOperand): string
    {
        return bcmul($leftOperand, $rightOperand, $this->scale);
    }

    public function pow(string $degreeBase, string $exponent): string
    {
        return bcpow($degreeBase, $exponent, $this->scale);
    }
}
