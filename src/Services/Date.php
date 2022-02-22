<?php

declare(strict_types=1);

namespace CommissionTask\Services;

use CommissionTask\Exceptions\CommissionTaskArgumentException;
use DateTime;

class Date
{
    /**
     * Parse date at format 'Y-m-d'.
     *
     * @throws CommissionTaskArgumentException
     */
    public function parseYmd(string $dateString): DateTime
    {
        $dateTime = DateTime::createFromFormat('Y-m-d', $dateString);

        if ($dateTime === false) {
            throw new CommissionTaskArgumentException('Invalid date format given');
        }

        return $dateTime;
    }
}
