<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Strategies\Traits;

use CommissionTask\Components\TransactionFeeCalculator\Exceptions\TransactionFeeCalculatorException;
use CommissionTask\Services\Math;

trait CommonCalculateOperations
{
    /**
     * Determine the scale of given amount.
     *
     * @throws TransactionFeeCalculatorException
     */
    private function determineScaleOfAmount(string $amount): int
    {
        $matches = [];
        $matchesCount = preg_match(self::FRACTIONAL_PART_REGEXP, $amount, $matches);

        if ($matchesCount === 1) {
            return strlen($matches[1]);
        } elseif ($matchesCount === 0) {
            return self::SCALE_NULL;
        } else {
            throw new TransactionFeeCalculatorException(
                TransactionFeeCalculatorException::UNDEFINED_SCALE_MESSAGE
            );
        }
    }

    /**
     * Round amount fractions up.
     */
    private function ceilAmount(string $amount): string
    {
        $ceilScale = $this->determineScaleOfAmount($amount) - self::ROUNDED_OFF_DIGITS_NUMBER;
        $lastDigits = substr($amount, -self::ROUNDED_OFF_DIGITS_NUMBER);
        $amount = substr($amount, 0, -self::ROUNDED_OFF_DIGITS_NUMBER);
        $previousLastDigitCharacter = substr($amount, -1);

        if ($previousLastDigitCharacter === '.') {
            $amount = substr($amount, 0, -1);
        }

        if (preg_match('/^0+$/', $lastDigits)) {
            $ceilMathService = new Math($ceilScale);

            $amount = $ceilMathService->add(
                $amount,
                $ceilMathService->pow(
                    $ceilMathService::NUMBER_SYSTEM_BASE,
                    (string)(-$ceilScale)
                )
            );
        }

        return $amount;
    }
}
