<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Exceptions\CommissionTaskArgumentException;
use DateTime;

class Date
{
    const DEFAULT_FORMAT = 'Y-m-d';

    /**
     * Parse date at given format.
     *
     * @throws CommissionTaskArgumentException
     */
    public function parseDate(string $dateString, string $format = self::DEFAULT_FORMAT): DateTime
    {
        $dateTime = DateTime::createFromFormat($format, $dateString);

        if ($dateTime === false) {
            throw new CommissionTaskArgumentException('Invalid date format given');
        }

        return $dateTime;
    }
}
