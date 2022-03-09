<?php

declare(strict_types=1);

namespace CommissionTask\Services;

class Math
{
    const NUMBER_SYSTEM_BASE = '10';
    const DECIMAL_SEPARATOR  = '.';
    const ZERO               = '0';
    const MIN_SCALE          = 0;

    const COMP_RESULT_LT      = -1;
    const COMP_RESULT_EQ      = 0;
    const COMP_RESULT_GT      = 1;
    const COMP_RESULTS_LTE    = [
        self::COMP_RESULT_LT,
        self::COMP_RESULT_EQ
    ];
    const COMP_RESULTS_GTE    = [
        self::COMP_RESULT_GT,
        self::COMP_RESULT_EQ
    ];

    /**
     * @var int
     */
    private $scale;

    public function __construct(int $scale)
    {
        $this->scale = $scale;
    }

    public function comp(string $leftOperand, string $rightOperand): int
    {
        return bccomp($leftOperand, $rightOperand, $this->scale);
    }

    public function add(string $leftOperand, string $rightOperand): string
    {
        return bcadd($leftOperand, $rightOperand, $this->scale);
    }

    public function sub(string $leftOperand, string $rightOperand): string
    {
        return bcsub($leftOperand, $rightOperand, $this->scale);
    }

    public function mul(string $leftOperand, string $rightOperand): string
    {
        return bcmul($leftOperand, $rightOperand, $this->scale);
    }

    public function pow(string $degreeBase, string $exponent): string
    {
        return bcpow($degreeBase, $exponent, $this->scale);
    }

    public function max(string $leftOperand, string $rightOperand): string
    {
        switch ($this->comp($leftOperand, $rightOperand)) {
            case self::COMP_RESULT_GT:
            case self::COMP_RESULT_EQ:
            default:
                return $leftOperand;
            case self::COMP_RESULT_LT:
                return $rightOperand;
        }
    }
}
