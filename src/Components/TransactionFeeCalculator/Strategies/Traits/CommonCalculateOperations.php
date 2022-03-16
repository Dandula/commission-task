<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Strategies\Traits;

use CommissionTask\Components\TransactionFeeCalculator\Exceptions\TransactionFeeCalculatorException;
use CommissionTask\Services\Config as ConfigService;
use CommissionTask\Services\Math as MathService;

trait CommonCalculateOperations
{
    /**
     * Get rounded off digits number.
     *
     * @throws TransactionFeeCalculatorException
     */
    private function getRoundedOffDigitsNumber(): int
    {
        return ConfigService::getConfigByName('feeCalculator.roundedOffDigitsNumber');
    }

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
        }

        if ($matchesCount === 0) {
            return self::SCALE_NULL;
        }

        throw new TransactionFeeCalculatorException(TransactionFeeCalculatorException::UNDEFINED_SCALE_MESSAGE);
    }

    /**
     * Round amount fractions up.
     */
    private function ceilAmount(string $amount): string
    {
        $roundedOffDigitsNumber = $this->getRoundedOffDigitsNumber();
        $ceilScale = max(
            $this->determineScaleOfAmount($amount) - $roundedOffDigitsNumber,
            MathService::MIN_SCALE
        );
        $lastDigits = substr($amount, offset: -$roundedOffDigitsNumber);
        $amount = substr($amount, offset: 0, length: -$roundedOffDigitsNumber);
        $previousLastDigitCharacter = substr($amount, offset: -1);

        if ($previousLastDigitCharacter === $this->mathService::DECIMAL_SEPARATOR) {
            $amount = substr($amount, offset: 0, length: -1);
        }

        if (!preg_match(self::NOT_ROUNDED_FRACTIONAL_PART_REGEXP, $lastDigits)) {
            $additionalCorrection = $this->mathService->pow(
                $this->mathService::NUMBER_SYSTEM_BASE,
                (string) (-$ceilScale),
                $ceilScale
            );

            $amount = $this->mathService->add(
                $amount,
                $additionalCorrection,
                $ceilScale
            );
        }

        return $amount;
    }
}
