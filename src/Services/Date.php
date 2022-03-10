<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Exceptions\CommissionTaskArgumentException;
use DateInterval;
use DateTime;

class Date
{
    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    const WEEK_MUTABLE_FORMAT_STRING = '%d days';

    const HOURS_MIN = 0;
    const MINUTES_MIN = 0;

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
     * Get now date.
     */
    public function getNow(): DateTime
    {
        return new DateTime();
    }

    /**
     * Get start of today.
     */
    public function getToday(): DateTime
    {
        return $this->getStartOfDay($this->getNow());
    }

    /**
     * Get start of day for given date.
     */
    public function getStartOfDay(DateTime $date): DateTime
    {
        $currentDate = clone $date;
        $currentDate->setTime(self::HOURS_MIN, self::MINUTES_MIN);

        return $currentDate;
    }

    /**
     * Get start of week for given date.
     */
    public function getStartOfWeek(DateTime $date): DateTime
    {
        $currentDate = $this->getStartOfDay($date);
        $dayOfWeekNumber = (int) $currentDate->format('N');

        return $this->subInterval(
            $currentDate,
            sprintf(self::WEEK_MUTABLE_FORMAT_STRING, $dayOfWeekNumber - 1)
        );
    }

    /**
     * Subtracts DateInterval given by datetime string.
     */
    public function subInterval(DateTime $date, string $datetime): DateTime
    {
        $currentDate = clone $date;
        $subInterval = DateInterval::createFromDateString($datetime);

        return $currentDate->sub($subInterval);
    }
}
