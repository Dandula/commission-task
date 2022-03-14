<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Repositories;

use CommissionTask\Components\Storage\ArrayStorage;
use CommissionTask\Components\Storage\Exceptions\Interfaces\StorageException;
use CommissionTask\Entities\Transaction;
use CommissionTask\Repositories\TransactionsRepository;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass TransactionsRepository
 */
final class TransactionsRepositoryTest extends TestCase
{
    /**
     * @var TransactionsRepository
     */
    private $transactionsRepository;

    public function setUp()
    {
        $arrayStorage = new ArrayStorage();
        $this->transactionsRepository = new TransactionsRepository($arrayStorage);
    }

    /**
     * @return TransactionsRepository
     */
    public function testEmpty()
    {
        $this->assertEmpty(
            $this->transactionsRepository->all()
        );

        return clone $this->transactionsRepository;
    }

    /**
     * @param TransactionsRepository $transactionsRepository
     *
     * @return TransactionsRepository
     *
     * @doesNotPerformAssertions
     * @depends testEmpty
     */
    public function testCreate(TransactionsRepository $transactionsRepository)
    {
        $transactionsRepository->create($this->getTransaction1());
        $transactionsRepository->create($this->getTransaction2());
        $transactionsRepository->create($this->getTransaction3());
        $transactionsRepository->create($this->getTransaction4());

        return clone $transactionsRepository;
    }

    /**
     * @param TransactionsRepository $transactionsRepository
     *
     * @depends testCreate
     */
    public function testAll(TransactionsRepository $transactionsRepository)
    {
        $actual = $transactionsRepository->all();

        $this->assertContainsOnlyInstancesOf(Transaction::class, $actual);
        $this->assertEquals([
            $this->getTransaction1(),
            $this->getTransaction2(),
            $this->getTransaction3(),
            $this->getTransaction4(),
        ], $actual);
    }

    /**
     * @param callable $filterMethod
     * @param Transaction[] $expectation
     * @param TransactionsRepository $transactionsRepository
     *
     * @dataProvider dataProviderForFilterTesting
     * @depends      testCreate
     */
    public function testFilter(
        callable $filterMethod,
        array $expectation,
        TransactionsRepository $transactionsRepository
    )
    {
        $this->assertEquals(
            $expectation,
            $transactionsRepository->filter($filterMethod)
        );
    }

    /**
     * @param TransactionsRepository $transactionsRepository
     *
     * @depends testCreate
     */
    public function testRead(TransactionsRepository $transactionsRepository)
    {
        $actual = $transactionsRepository->read(1);

        $this->assertEquals($this->getTransaction2(), $actual);
    }

    /**
     * @param TransactionsRepository $transactionsRepository
     *
     * @return TransactionsRepository
     *
     * @doesNotPerformAssertions
     * @depends testCreate
     */
    public function testDelete(TransactionsRepository $transactionsRepository): TransactionsRepository
    {
        $transactionsRepository->delete(1);
        
        return clone $transactionsRepository;
    }

    /**
     * @param TransactionsRepository $transactionsRepository
     *
     * @depends testDelete
     */
    public function testReadFailure(TransactionsRepository $transactionsRepository)
    {
        $this->expectException(StorageException::class);

        $transactionsRepository->read(1);
    }

    /**
     * @param TransactionsRepository $transactionsRepository
     *
     * @return TransactionsRepository
     *
     * @doesNotPerformAssertions
     * @depends testCreate
     */
    public function testDeleteAll(TransactionsRepository $transactionsRepository): TransactionsRepository
    {
        $transactionsRepository->deleteAll();

        return clone $transactionsRepository;
    }

    /**
     * @param TransactionsRepository $transactionsRepository
     *
     * @depends testDeleteAll
     */
    public function testDeletedAll(TransactionsRepository $transactionsRepository)
    {
        $this->assertEmpty(
            $this->transactionsRepository->all()
        );
    }

    public function dataProviderForFilterTesting(): array
    {
        $filterByUserType = function (Transaction $transaction) {
            return $transaction->getUserType() === Transaction::USER_TYPE_PRIVATE;
        };

        $filterByTransactionType = function (Transaction $transaction) {
            return $transaction->getType() === Transaction::TYPE_DEPOSIT;
        };

        $filterByUserId = function (Transaction $transaction) {
            return $transaction->getUserId() === 2;
        };

        $filterByCurrencyCode = function (Transaction $transaction) {
            return $transaction->getCurrencyCode() === 'EUR';
        };

        return [
            'filter transactions with private users' => [
                $filterByUserType,
                [
                    0 => $this->getTransaction1(),
                    1 => $this->getTransaction2(),
                    2 => $this->getTransaction3(),
                ]
            ],
            'filter transactions with deposit' => [
                $filterByTransactionType,
                [
                    0 => $this->getTransaction1(),
                    3 => $this->getTransaction4(),
                ]
            ],
            'filter transactions with user with ID=2' => [
                $filterByUserId,
                [
                    3 => $this->getTransaction4(),
                ]
            ],
            'filter transactions with currency EUR' => [
                $filterByCurrencyCode,
                [
                    1 => $this->getTransaction2(),
                    3 => $this->getTransaction4(),
                ]
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
        $transaction->setType(Transaction::TYPE_DEPOSIT);
        $transaction->setAmount('3000.00');
        $transaction->setCurrencyCode('EUR');

        return $transaction;
    }
}
