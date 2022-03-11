<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Services;

use CommissionTask\Exceptions\CommissionTaskArgumentException;
use CommissionTask\Services\Date as DateService;
use DateTime;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    /**
     * @var DateService
     */
    private $dateService;

    public function setUp()
    {
        $this->dateService = new DateService();
    }

    /**
     * @param string $dateString
     * @param string $format
     * @param string $expectation
     *
     * @dataProvider dataProviderForParseDateTesting
     */
    public function testParseDate(string $dateString, string $format, string $expectation)
    {
        $this->assertInstanceOf(
            $expectation,
            $this->dateService->parseDate($dateString, $format)
        );
    }

    /**
     * @param string $dateString
     * @param string $expectation
     *
     * @dataProvider dataProviderForParseDateInDefaultFormatTesting
     */
    public function testParseDateInDefaultFormat(string $dateString, string $expectation)
    {
        $this->assertInstanceOf(
            $expectation,
            $this->dateService->parseDate($dateString)
        );
    }

    /**
     * @param string $dateString
     * @param string $format
     * @param string $expectException
     * @param string $expectExceptionMessage
     *
     * @dataProvider dataProviderForParseDateFailureTesting
     */
    public function testParseDateFailure(
        string $dateString,
        string $format,
        string $expectException,
        string $expectExceptionMessage
    )
    {
        $this->expectException($expectException);
        $this->expectExceptionMessage($expectExceptionMessage);

        $this->dateService->parseDate($dateString, $format);
    }

    /**
     * @param string $expectation
     *
     * @dataProvider dataProviderForGetNowTesting
     */
    public function testGetNow(string $expectation)
    {
        $this->assertInstanceOf(
            $expectation,
            $this->dateService->getNow()
        );
    }

    /**
     * @param string $datetimeString
     * @param string $expectation
     *
     * @dataProvider dataProviderForGetTodayTesting
     */
    public function testGetToday(string $datetimeString, string $expectation)
    {
        $mockDateService = $this->getMockBuilder(DateService::class)
            ->setMethods(['getNow'])
            ->getMock();

        $mockDateService->expects($this->any())
            ->method('getNow')
            ->willReturn(DateTime::createFromFormat('Y-m-d H:i:s', $datetimeString));

        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d H:i:s', $expectation),
            $mockDateService->getToday()
        );
    }

    /**
     * @param string $date
     * @param string $expectation
     *
     * @dataProvider dataProviderForGetStartOfDayTesting
     */
    public function testGetStartOfDay(string $date, string $expectation)
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        $expectation = DateTime::createFromFormat('Y-m-d H:i:s', $expectation);
        $actual = $this->dateService->getStartOfDay($date);

        $this->assertEquals($expectation, $actual);
        $this->assertNotSame($date, $actual);
    }

    /**
     * @param string $format
     * @param string $date
     * @param string $expectation
     *
     * @dataProvider dataProviderForGetStartOfWeekTesting
     */
    public function testGetStartOfWeek(string $format, string $date, string $expectation)
    {
        $date = DateTime::createFromFormat($format, $date);
        $expectation = DateTime::createFromFormat('Y-m-d H:i:s', $expectation);
        $actual = $this->dateService->getStartOfWeek($date);

        $this->assertEquals($expectation, $actual);
        $this->assertNotSame($date, $actual);
    }

    /**
     * @param string $date
     * @param string $relativeDatetime
     * @param string $expectation
     *
     * @dataProvider dataProviderForSubIntervalTesting
     */
    public function testSubInterval(string $date, string $relativeDatetime, string $expectation)
    {
        $format = 'Y-m-d H:i:s';
        $date = DateTime::createFromFormat($format, $date);
        $expectation = DateTime::createFromFormat($format, $expectation);
        $actual = $this->dateService->subInterval($date, $relativeDatetime);

        $this->assertEquals($expectation, $actual);
        $this->assertNotSame($date, $actual);
    }

    public function dataProviderForParseDateTesting(): array
    {
        return [
            "parse date in format 'Y-m-d'" => ['2022-02-24', 'Y-m-d', DateTime::class],
            "parse date and time in format 'Y-m-d H:i:s'" => ['2022-02-24 02:00:00', 'Y-m-d H:i:s', DateTime::class],
        ];
    }

    public function dataProviderForParseDateInDefaultFormatTesting(): array
    {
        return [
            'parse date in default format' => ['2022-02-24', DateTime::class],
        ];
    }

    public function dataProviderForParseDateFailureTesting(): array
    {
        return [
            'try to parse date in invalid format' => [
                '24-02-2022',
                'Y-m-d',
                CommissionTaskArgumentException::class,
                CommissionTaskArgumentException::INVALID_DATE_FORMAT_MESSAGE
            ],
        ];
    }

    public function dataProviderForGetNowTesting(): array
    {
        return [
            'get now datetime' => [DateTime::class],
        ];
    }

    public function dataProviderForGetTodayTesting(): array
    {
        return [
            'get today datetime' => ['2022-02-24 04:00:00', '2022-02-24 00:00:00'],
        ];
    }

    public function dataProviderForGetStartOfDayTesting(): array
    {
        return [
            'get start of day' => ['2022-02-21 11:12:13', '2022-02-21 00:00:00'],
        ];
    }

    public function dataProviderForGetStartOfWeekTesting(): array
    {
        return [
            'get start of week' => ['Y-m-d', '2022-02-24', '2022-02-21 00:00:00'],
            'get the beginning of the week in the previous month' => ['Y-m-d', '2022-02-03', '2022-01-31 00:00:00'],
            'get start of week with time' => ['Y-m-d H:i:s', '2022-02-24 02:00:00', '2022-02-21 00:00:00'],
        ];
    }

    public function dataProviderForSubIntervalTesting(): array
    {
        return [
            'subtracts days' => ['2022-02-24 02:00:00', '2 days', '2022-02-22 02:00:00'],
            'subtracts hours' => ['2022-02-24 02:00:00', '10 hours', '2022-02-23 16:00:00'],
            'subtracts days and hours' => ['2022-02-24 02:00:00', '2 days 10 hours', '2022-02-21 16:00:00'],
        ];
    }
}
