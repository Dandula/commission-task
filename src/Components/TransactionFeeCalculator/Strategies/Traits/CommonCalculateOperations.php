<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Strategies\Traits;

use CommissionTask\Components\TransactionFeeCalculator\Exceptions\TransactionFeeCalculatorLogicException;

trait CommonCalculateOperations
{
    /**
     * Get rounded off digits number.
     */
    private function getRoundedOffDigitsNumber(): int
    {
        return $this->configService->getConfigByName('feeCalculator.roundedOffDigitsNumber');
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
            throw new TransactionFeeCalculatorLogicException(TransactionFeeCalculatorLogicException::INSUFFICIENT_ACCURACY_MESSAGE);
        }

        // Discard the rounded digits
        $amount = substr($amount, offset: 0, length: -$roundedOffDigitsNumber);

        // If the last character before the rounded digits is a decimal separator, discard it
        if (str_ends_with($amount, $this->mathService::FRACTION_SEPARATOR)) {
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
