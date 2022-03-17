<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Strategies\Traits;

use CommissionTask\Components\TransactionFeeCalculator\Exceptions\TransactionFeeCalculatorException;
use CommissionTask\Services\Config as ConfigService;

trait CommonCalculateOperations
{
    /**
     * Get rounded off digits number.
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
     * Round amount fractions upwards.
     */
    private function ceilAmount(string $amount, int $ceilScale): string
    {
        // Get the number of rounded digits
        $roundedOffDigitsNumber = $this->getRoundedOffDigitsNumber();

        // If there are no rounded digits, return the amount as it is
        if ($roundedOffDigitsNumber === 0) {
            return $amount;
        }

        // Get the digits from the rounded digits
        $lastDigits = substr($amount, offset: -$roundedOffDigitsNumber);

        // Check if there are enough digits in the amount for rounding
        if (str_contains($lastDigits, $this->mathService::FRACTION_SEPARATOR)) {
            throw new TransactionFeeCalculatorException(TransactionFeeCalculatorException::INSUFFICIENT_ACCURACY_MESSAGE);
        }

        // Discard the rounded digits
        $amount = substr($amount, offset: 0, length: -$roundedOffDigitsNumber);
        // Get the last character before the rounded digits
        $previousLastDigitCharacter = substr($amount, offset: -1);

        // If the last character before the rounded digits is a decimal separator, discard it
        if ($previousLastDigitCharacter === $this->mathService::FRACTION_SEPARATOR) {
            $amount = substr($amount, offset: 0, length: -1);
        }

        // If the rounded digits are not zero, round the last digit upwards
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
