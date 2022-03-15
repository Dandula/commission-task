<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Factories;

use CommissionTask\Components\TransactionFeeCalculator\Strategies\DepositStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\WithdrawBusinessStrategy;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\WithdrawPrivateStrategy;
use CommissionTask\Entities\Transaction;
use CommissionTask\Factories\Exceptions\TransactionFeeCalculatorStrategyFactoryException;
use CommissionTask\Factories\TransactionFeeCalculatorStrategyFactory;
use CommissionTask\Repositories\TransactionsRepository;
use CommissionTask\Services\Currency as CurrencyService;
use CommissionTask\Services\Date as DateService;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass TransactionFeeCalculatorStrategyFactory
 */
final class TransactionFeeCalculatorStrategyFactoryTest extends TestCase
{
    /**
     * @var TransactionFeeCalculatorStrategyFactory
     */
    private $transactionFeeCalculatorStrategyFactory;

    protected function setUp(): void
    {
        $mockTransactionsRepository = $this->getMockBuilder(TransactionsRepository::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'all',
                'filter',
                'read',
                'create',
                'delete',
                'deleteAll',
            ])
            ->getMock();

        $mockDateService = $this->getMockBuilder(DateService::class)
            ->getMock();

        $mockCurrencyService = $this->getMockBuilder(CurrencyService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transactionFeeCalculatorStrategyFactory = new TransactionFeeCalculatorStrategyFactory(
            $mockTransactionsRepository,
            $mockDateService,
            $mockCurrencyService
        );
    }

    /**
     * @param Transaction $transaction
     * @param string $expectation
     *
     * @dataProvider dataProviderForGetTransactionFeeCalculatorStrategyTesting
     */
    public function testGetTransactionFeeCalculatorStrategy(
        Transaction $transaction,
        string $expectation
    )
    {
        $this->assertInstanceOf(
            $expectation,
            $this->transactionFeeCalculatorStrategyFactory->getTransactionFeeCalculatorStrategy($transaction)
        );
    }

    /**
     * @param Transaction $transaction
     * @param string $expectException
     * @param string $expectMessage
     *
     * @dataProvider dataProviderForGetTransactionFeeCalculatorStrategyFailureTesting
     */
    public function testGetTransactionFeeCalculatorStrategyFailure(
        Transaction $transaction,
        string $expectException,
        string $expectExceptionMessage
    )
    {
        $this->expectException($expectException);
        $this->expectExceptionMessage($expectExceptionMessage);

        $this->transactionFeeCalculatorStrategyFactory->getTransactionFeeCalculatorStrategy($transaction);
    }

    public function dataProviderForGetTransactionFeeCalculatorStrategyTesting(): array
    {
        return [
            'get strategy for transaction #1' => [$this->getTransaction1(), DepositStrategy::class],
            'get strategy for transaction #2' => [$this->getTransaction2(), WithdrawPrivateStrategy::class],
            'get strategy for transaction #3' => [$this->getTransaction3(), WithdrawPrivateStrategy::class],
            'get strategy for transaction #4' => [$this->getTransaction4(), WithdrawBusinessStrategy::class],
        ];
    }

    public function dataProviderForGetTransactionFeeCalculatorStrategyFailureTesting(): array
    {
        return [
            'get strategy for transaction #5' => [
                $this->getTransaction5(),
                TransactionFeeCalculatorStrategyFactoryException::class,
                TransactionFeeCalculatorStrategyFactoryException::UNDEFINED_TRANSACTION_TYPE_MESSAGE,
            ],
            'get strategy for transaction #6' => [
                $this->getTransaction6(),
                TransactionFeeCalculatorStrategyFactoryException::class,
                TransactionFeeCalculatorStrategyFactoryException::UNDEFINED_USER_TYPE_MESSAGE,
            ],
        ];
    }

    private static function getTransaction1(): Transaction
    {
        $date = new DateTime('2022-02-21');

        $transaction = new Transaction();
        $transaction->setDate($date);
        $transaction->setUserId(1);
        $transaction->setUserType(Transaction::USER_TYPE_PRIVATE);
        $transaction->setType(Transaction::TYPE_DEPOSIT);
        $transaction->setAmount('2000.00');
        $transaction->setCurrencyCode('USD');

        return $transaction;
    }

    private static function getTransaction2(): Transaction
    {
        $date = new DateTime('2022-02-23');

        $transaction = new Transaction();
        $transaction->setDate($date);
        $transaction->setUserId(1);
        $transaction->setUserType(Transaction::USER_TYPE_PRIVATE);
        $transaction->setType(Transaction::TYPE_WITHDRAW);
        $transaction->setAmount('1000.00');
        $transaction->setCurrencyCode('EUR');

        return $transaction;
    }

    private static function getTransaction3(): Transaction
    {
        $date = new DateTime('2022-02-24');

        $transaction = new Transaction();
        $transaction->setDate($date);
        $transaction->setUserId(1);
        $transaction->setUserType(Transaction::USER_TYPE_PRIVATE);
        $transaction->setType(Transaction::TYPE_WITHDRAW);
        $transaction->setAmount('30000');
        $transaction->setCurrencyCode('JPY');

        return $transaction;
    }

    private static function getTransaction4(): Transaction
    {
        $date = new DateTime('2022-02-26');

        $transaction = new Transaction();
        $transaction->setDate($date);
        $transaction->setUserId(2);
        $transaction->setUserType(Transaction::USER_TYPE_BUSINESS);
        $transaction->setType(Transaction::TYPE_WITHDRAW);
        $transaction->setAmount('3000.00');
        $transaction->setCurrencyCode('EUR');

        return $transaction;
    }

    private static function getTransaction5(): Transaction
    {
        $date = new DateTime('2022-02-26');

        $transaction = new Transaction();
        $transaction->setDate($date);
        $transaction->setUserId(2);
        $transaction->setUserType(Transaction::USER_TYPE_BUSINESS);
        $transaction->setType('undefined type');
        $transaction->setAmount('3000.00');
        $transaction->setCurrencyCode('EUR');

        return $transaction;
    }

    private static function getTransaction6(): Transaction
    {
        $date = new DateTime('2022-02-26');

        $transaction = new Transaction();
        $transaction->setDate($date);
        $transaction->setUserId(2);
        $transaction->setUserType('undefined user type');
        $transaction->setType(Transaction::TYPE_WITHDRAW);
        $transaction->setAmount('3000.00');
        $transaction->setCurrencyCode('EUR');

        return $transaction;
    }
}
