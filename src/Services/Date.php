<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Exceptions\CommissionTaskArgumentException;
use DateInterval;
use DateTime;

class Date
{
    const DEFAULT_DATE_FORMAT        = 'Y-m-d';
    const WEEK_MUTABLE_FORMAT_STRING = '%d days';

    /**
     * Parse date at given format.
     *
     * @throws CommissionTaskArgumentException
     */
    public function parseDate(string $dateString, string $format = self::DEFAULT_DATE_FORMAT): DateTime
    {
        $dateTime = DateTime::createFromFormat($format, $dateString);

        if ($dateTime === false) {
            throw new CommissionTaskArgumentException('Invalid date format given');
        }

        return $dateTime;
    }

    /**
     * Get start of week for given date.
     */
    public function getStartOfWeek(DateTime $date): DateTime
    {
        $currentDate = clone $date;
        $dayOfWeekNumber = (int)$currentDate->format('N');
        $subInterval = DateInterval::createFromDateString(
            sprintf(self::WEEK_MUTABLE_FORMAT_STRING, $dayOfWeekNumber - 1)
        );
        return $currentDate->sub($subInterval);
    }
}
