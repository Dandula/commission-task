<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Strategies;

use CommissionTask\Components\TransactionFeeCalculator\Strategies\Interfaces\TransactionFeeCalculateStrategy as TransactionFeeCalculateStrategyContract;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\Traits\CommonCalculateOperations;
use CommissionTask\Entities\Transaction;
use CommissionTask\Repositories\Interfaces\TransactionsRepository;
use CommissionTask\Services\Currency;
use CommissionTask\Services\Date;
use CommissionTask\Services\Math;

class WithdrawPrivateStrategy implements TransactionFeeCalculateStrategyContract
{
    use CommonCalculateOperations;

    const FEE_RATE                 = '0.003';
    const FREE_LIMIT_AMOUNT        = '1000';
    const FREE_LIMIT_CURRENCY_CODE = 'EUR';
    const FREE_OPERATIONS_NUMBER   = 3;

    /**
     * @var TransactionsRepository
     */
    private $transactionsRepository;

    /**
     * @var Date
     */
    private $dateService;

    /**
     * @var Currency
     */
    private $currencyService;

    /**
     * Create a new transaction fee calculator strategy instance for withdraw transactions of private user.
     *
     * @param TransactionsRepository $transactionsRepository
     * @param Date $dateService
     * @param Currency $currencyService
     */
    public function __construct(
        TransactionsRepository $transactionsRepository,
        Date $dateService,
        Currency $currencyService
    )
    {
        $this->transactionsRepository = $transactionsRepository;
        $this->dateService = $dateService;
        $this->currencyService = $currencyService;
    }

    /**
     * @inheritDoc
     */
    public function calculateTransactionFee(Transaction $transaction): string
    {
        $amount = $transaction->getAmount();
        $mathService = new Math($this->determineScaleOfAmount($amount) + self::ROUNDED_OFF_DIGITS_NUMBER);

        $influentialTransactions = $this->getInfluentialTransactions($transaction);

        if (count($influentialTransactions) >= self::FREE_OPERATIONS_NUMBER) {
            $taxableAmount = $amount;
        } else {
            $taxableAmount = $this->getTaxableAmount($transaction, $influentialTransactions);
        }

        $feeAmount = $mathService->mul($taxableAmount, self::FEE_RATE);

        return $this->ceilAmount($feeAmount);
    }

    /**
     * Get transactions affecting the fee calculation.
     *
     * @return Transaction[]
     */
    private function getInfluentialTransactions(Transaction $transaction): array
    {
        $transactionUserId = $transaction->getUserId();
        $transactionDate = $transaction->getDate();
        $transactionDateStartOfWeek = $this->dateService->getStartOfWeek($transactionDate);

        $influentialTransactionsFilterMethod = function (Transaction $checkedTransaction)
            use ($transactionUserId, $transactionDate, $transactionDateStartOfWeek) {
                return $checkedTransaction->getUserId() === $transactionUserId
                    && $checkedTransaction->getDate() >= $transactionDateStartOfWeek
                    && $checkedTransaction->getDate() < $transactionDate;
            };

        return $this->transactionsRepository->filter($influentialTransactionsFilterMethod);
    }

    /**
     * Get taxable amount of given transaction.
     *
     * @param Transaction $transaction
     * @param Transaction[] $influentialTransactions
     * @return string
     */
    private function getTaxableAmount(Transaction $transaction, array $influentialTransactions): string
    {
        return $transaction->getAmount();
    }
}
