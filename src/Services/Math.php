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
    public const COMP_RESULTS_LTE = [
        self::COMP_RESULT_LT,
        self::COMP_RESULT_EQ,
    ];
    public const COMP_RESULTS_GTE = [
        self::COMP_RESULT_GT,
        self::COMP_RESULT_EQ,
    ];

    /**
     * Create math service instance.
     */
    public function __construct(private int $scale)
    {
    }

    /**
     * Compare two numbers.
     */
    public function comp(string $leftOperand, string $rightOperand): int
    {
        return bccomp($leftOperand, $rightOperand, $this->scale);
    }

    /**
     * Add two numbers.
     */
    public function add(string $leftOperand, string $rightOperand): string
    {
        return bcadd($leftOperand, $rightOperand, $this->scale);
    }

    /**
     * Subtract one number from another.
     */
    public function sub(string $leftOperand, string $rightOperand): string
    {
        return bcsub($leftOperand, $rightOperand, $this->scale);
    }

    /**
     * Multiply two numbers.
     */
    public function mul(string $leftOperand, string $rightOperand): string
    {
        return bcmul($leftOperand, $rightOperand, $this->scale);
    }

    /**
     * Raise number to another.
     */
    public function pow(string $degreeBase, string $exponent): string
    {
        return bcpow($degreeBase, $exponent, $this->scale);
    }

    /**
     * Find the highest number.
     */
    public function max(string $operand, string ...$otherOperands): string
    {
        $maxOperand = $operand;

        foreach ($otherOperands as $anotherOperand) {
            switch ($this->comp($maxOperand, $anotherOperand)) {
                case self::COMP_RESULT_GT:
                case self::COMP_RESULT_EQ:
                default:
                    break;
                case self::COMP_RESULT_LT:
                    $maxOperand = $anotherOperand;
                    break;
            }
        }

        return $maxOperand;
    }
}
