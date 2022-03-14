<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Factories;

use CommissionTask\Components\DataFormatter\CsvTransactionDataFormatter;
use CommissionTask\Entities\Transaction;
use CommissionTask\Factories\CsvTransactionFactory;
use CommissionTask\Services\Date as DateService;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass CsvTransactionFactory
 */
final class CsvTransactionFactoryTest extends TestCase
{
    /**
     * @var CsvTransactionFactory
     */
    private $csvTransactionFactory;

    public function setUp()
    {
        $csvTransactionDataFormatter = new CsvTransactionDataFormatter();
        $dateService = new DateService();
        $this->csvTransactionFactory = new CsvTransactionFactory($csvTransactionDataFormatter, $dateService);
    }

    /**
     * @param mixed $transactionData
     * @param DateTime $expectDate
     * @param int $expectUserId
     * @param string $expectUserType
     * @param string $expectType
     * @param string $expectAmount
     * @param string $expectCurrencyCode
     *
     * @dataProvider dataProviderForMakeCurrencyTesting
     */
    public function testMakeTransaction(
        $transactionData,
        DateTime $expectDate,
        int $expectUserId,
        string $expectUserType,
        string $expectType,
        string $expectAmount,
        string $expectCurrencyCode
    )
    {
        $actual = $this->csvTransactionFactory->makeTransaction($transactionData);

        $this->assertInstanceOf(Transaction::class, $actual);
        $this->assertEquals($expectDate, $actual->getDate());
        $this->assertEquals($expectUserId, $actual->getUserId());
        $this->assertEquals($expectUserType, $actual->getUserType());
        $this->assertEquals($expectType, $actual->getType());
        $this->assertEquals($expectAmount, $actual->getAmount());
        $this->assertEquals($expectCurrencyCode, $actual->getCurrencyCode());
    }

    public function dataProviderForMakeCurrencyTesting(): array
    {
        $csvTransactionDataFormatter = new CsvTransactionDataFormatter();

        return [
            'make transaction #1' => [
                [
                    $csvTransactionDataFormatter::COLUMN_DATE_NUMBER => '2022-02-21',
                    $csvTransactionDataFormatter::COLUMN_USER_ID_NUMBER => '1',
                    $csvTransactionDataFormatter::COLUMN_USER_TYPE_NUMBER => Transaction::USER_TYPE_PRIVATE,
                    $csvTransactionDataFormatter::COLUMN_TYPE_NUMBER => Transaction::TYPE_DEPOSIT,
                    $csvTransactionDataFormatter::COLUMN_AMOUNT_NUMBER => '2000.00',
                    $csvTransactionDataFormatter::COLUMN_CURRENCY_CODE_NUMBER => 'USD',
                ],
                new DateTime('2022-02-21'),
                1,
                Transaction::USER_TYPE_PRIVATE,
                Transaction::TYPE_DEPOSIT,
                '2000.00',
                'USD'
            ],
            'make transaction #2' => [
                [
                    $csvTransactionDataFormatter::COLUMN_DATE_NUMBER => '2022-02-23',
                    $csvTransactionDataFormatter::COLUMN_USER_ID_NUMBER => '1',
                    $csvTransactionDataFormatter::COLUMN_USER_TYPE_NUMBER => Transaction::USER_TYPE_PRIVATE,
                    $csvTransactionDataFormatter::COLUMN_TYPE_NUMBER => Transaction::TYPE_WITHDRAW,
                    $csvTransactionDataFormatter::COLUMN_AMOUNT_NUMBER => '1000.00',
                    $csvTransactionDataFormatter::COLUMN_CURRENCY_CODE_NUMBER => 'EUR',
                ],
                new DateTime('2022-02-23'),
                1,
                Transaction::USER_TYPE_PRIVATE,
                Transaction::TYPE_WITHDRAW,
                '1000.00',
                'EUR'
            ],
            'make transaction #3' => [
                [
                    $csvTransactionDataFormatter::COLUMN_DATE_NUMBER => '2022-02-24',
                    $csvTransactionDataFormatter::COLUMN_USER_ID_NUMBER => '1',
                    $csvTransactionDataFormatter::COLUMN_USER_TYPE_NUMBER => Transaction::USER_TYPE_PRIVATE,
                    $csvTransactionDataFormatter::COLUMN_TYPE_NUMBER => Transaction::TYPE_WITHDRAW,
                    $csvTransactionDataFormatter::COLUMN_AMOUNT_NUMBER => '30000',
                    $csvTransactionDataFormatter::COLUMN_CURRENCY_CODE_NUMBER => 'JPY',
                ],
                new DateTime('2022-02-24'),
                1,
                Transaction::USER_TYPE_PRIVATE,
                Transaction::TYPE_WITHDRAW,
                '30000',
                'JPY'
            ],
            'make transaction #4' => [
                [
                    $csvTransactionDataFormatter::COLUMN_DATE_NUMBER => '2022-02-26',
                    $csvTransactionDataFormatter::COLUMN_USER_ID_NUMBER => '2',
                    $csvTransactionDataFormatter::COLUMN_USER_TYPE_NUMBER => Transaction::USER_TYPE_BUSINESS,
                    $csvTransactionDataFormatter::COLUMN_TYPE_NUMBER => Transaction::TYPE_WITHDRAW,
                    $csvTransactionDataFormatter::COLUMN_AMOUNT_NUMBER => '3000.00',
                    $csvTransactionDataFormatter::COLUMN_CURRENCY_CODE_NUMBER => 'EUR',
                ],
                new DateTime('2022-02-26'),
                2,
                Transaction::USER_TYPE_BUSINESS,
                Transaction::TYPE_WITHDRAW,
                '3000.00',
                'EUR'
            ],
        ];
    }
}
