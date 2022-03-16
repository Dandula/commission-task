<?php

declare(strict_types=1);

namespace CommissionTask\Services;

class Math
{
    public const NUMBER_SYSTEM_BASE = '10';
    public const DECIMAL_SEPARATOR = '.';
    public const ZERO = '0';
    public const MIN_SCALE = 0;

    public const COMP_RESULT_LT = -1;
    public const COMP_RESULT_EQ = 0;
    public const COMP_RESULT_GT = 1;

    /**
     * Compare two numbers.
     */
    public function comp(string $leftOperand, string $rightOperand, int $scale): int
    {
        return bccomp($leftOperand, $rightOperand, $scale);
    }

    /**
     * Add two numbers.
     */
    public function add(string $leftOperand, string $rightOperand, int $scale): string
    {
        return bcadd($leftOperand, $rightOperand, $scale);
    }

    /**
     * Subtract one number from another.
     */
    public function sub(string $leftOperand, string $rightOperand, int $scale): string
    {
        return bcsub($leftOperand, $rightOperand, $scale);
    }

    /**
     * Multiply two numbers.
     */
    public function mul(string $leftOperand, string $rightOperand, int $scale): string
    {
        return bcmul($leftOperand, $rightOperand, $scale);
    }

    /**
     * Raise number to another.
     */
    public function pow(string $degreeBase, string $exponent, int $scale): string
    {
        return bcpow($degreeBase, $exponent, $scale);
    }

    /**
     * Find the highest number.
     */
    public function max(string $leftOperand, string $rightOperand, int $scale): string
    {
        switch ($this->comp($leftOperand, $rightOperand, $scale)) {
            case self::COMP_RESULT_GT:
            case self::COMP_RESULT_EQ:
            default:
                return $leftOperand;
            case self::COMP_RESULT_LT:
                return $rightOperand;
        }
    }
}
