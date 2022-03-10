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

    const FEE_RATE = '0.003';
    const NONTAXABLE_AMOUNT = '1000.00';
    const NONTAXABLE_CURRENCY_CODE = 'EUR';
    const FREE_OPERATIONS_NUMBER = 3;

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
     */
    public function __construct(
        TransactionsRepository $transactionsRepository,
        Date $dateService,
        Currency $currencyService
    ) {
        $this->transactionsRepository = $transactionsRepository;
        $this->dateService = $dateService;
        $this->currencyService = $currencyService;
    }

    /**
     * {@inheritDoc}
     */
    public function calculateTransactionFee(Transaction $transaction): string
    {
        $amount = $transaction->getAmount();
        $mathService = new Math(
            $this->determineScaleOfAmount($amount) + self::ROUNDED_OFF_DIGITS_NUMBER
        );

        $influentialTransactions = $this->getInfluentialTransactions($transaction);

        $taxableAmount = $this->getTaxableAmount($transaction, $influentialTransactions, $mathService);

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
        $transactionType = $transaction::TYPE_WITHDRAW;
        $transactionUserType = $transaction::USER_TYPE_PRIVATE;

        $influentialTransactionsFilterMethod = function (Transaction $checkedTransaction) use ($transactionUserId, $transactionDate, $transactionDateStartOfWeek, $transactionType, $transactionUserType) {
            return $checkedTransaction->getUserId() === $transactionUserId
                    && $checkedTransaction->getDate() >= $transactionDateStartOfWeek
                    && $checkedTransaction->getDate() < $transactionDate
                    && $checkedTransaction->getType() === $transactionType
                    && $checkedTransaction->getUserType() === $transactionUserType;
        };

        return $this->transactionsRepository->filter($influentialTransactionsFilterMethod);
    }

    /**
     * Get taxable amount of given transaction.
     *
     * @param Transaction[] $influentialTransactions
     */
    private function getTaxableAmount(
        Transaction $transaction,
        array $influentialTransactions,
        Math $transactionMathService
    ): string {
        $transactionAmount = $transaction->getAmount();

        // If more than 3 transactions charge commission for the full amount
        if (count($influentialTransactions) >= self::FREE_OPERATIONS_NUMBER) {
            return $transactionAmount;
        }

        $nontaxableAmountMathService = new Math(
            $this->determineScaleOfAmount(self::NONTAXABLE_AMOUNT) + self::ROUNDED_OFF_DIGITS_NUMBER
        );

        // Calculate the non-taxable amount in the currency of the non-taxable limit
        $lostNontaxableAmount = $this->calculateLostNontaxableAmount(
            $influentialTransactions,
            $nontaxableAmountMathService
        );

        // If the non-taxable limit is exhausted, charge the commission for the full amount
        if ($nontaxableAmountMathService->comp($lostNontaxableAmount, $nontaxableAmountMathService::ZERO)
            === $nontaxableAmountMathService::COMP_RESULT_EQ) {
            return $transactionAmount;
        }

        $transactionCurrencyCode = $transaction->getCurrencyCode();

        // Calculate the taxable amount subtracting the non-taxable amount
        return $this->calculateTaxableAmount(
            $transactionAmount,
            $lostNontaxableAmount,
            $transactionCurrencyCode,
            $transactionMathService,
            $nontaxableAmountMathService
        );
    }

    /**
     * Calculate lost non-taxable amount.
     *
     * @param Transaction[] $influentialTransactions
     */
    private function calculateLostNontaxableAmount(
        array $influentialTransactions,
        Math $nontaxableAmountMathService
    ): string {
        $lostNontaxableAmount = self::NONTAXABLE_AMOUNT;

        // Subtracts the amount of previous transactions for the week from the non-taxable amount
        while ($influentialTransactions
            && $nontaxableAmountMathService->comp($lostNontaxableAmount, $nontaxableAmountMathService::ZERO)
                === $nontaxableAmountMathService::COMP_RESULT_GT) {
            $influentialTransaction = array_shift($influentialTransactions);
            $influentialTransactionAmountAtNontaxableCurrency = $this->currencyService->convertAmountToCurrency(
                $influentialTransaction->getAmount(),
                $influentialTransaction->getCurrencyCode(),
                self::NONTAXABLE_CURRENCY_CODE,
                $nontaxableAmountMathService
            );
            $lostNontaxableAmount = $nontaxableAmountMathService->sub($lostNontaxableAmount, $influentialTransactionAmountAtNontaxableCurrency);
        }

        return $nontaxableAmountMathService->max($lostNontaxableAmount, $nontaxableAmountMathService::ZERO);
    }

    /**
     * Calculate the taxable amount subtracting the non-taxable amount.
     */
    private function calculateTaxableAmount(
        string $transactionAmount,
        string $lostNontaxableAmount,
        string $transactionCurrencyCode,
        Math $transactionMathService,
        Math $nontaxableAmountMathService
    ): string {
        // Convert the transaction amount to the currency of the non-taxable limit
        $transactionAmountAtNontaxableCurrency = $this->currencyService->convertAmountToCurrency(
            $transactionAmount,
            $transactionCurrencyCode,
            self::NONTAXABLE_CURRENCY_CODE,
            $nontaxableAmountMathService
        );

        // Calculate the part of the transaction amount taxed by the commission in the currency of the non-taxable limit
        $taxableAmountAtNontaxableCurrency = $nontaxableAmountMathService->sub(
            $transactionAmountAtNontaxableCurrency,
            $lostNontaxableAmount
        );
        $taxableAmountAtNontaxableCurrency = $nontaxableAmountMathService->max(
            $taxableAmountAtNontaxableCurrency,
            $nontaxableAmountMathService::ZERO
        );

        // Convert the taxable amount to the currency of the transaction
        $taxableAmount = $this->currencyService->convertAmountToCurrency(
            $taxableAmountAtNontaxableCurrency,
            self::NONTAXABLE_CURRENCY_CODE,
            $transactionCurrencyCode,
            $transactionMathService
        );

        // Round up the taxable amount and return it
        return $this->ceilAmount($taxableAmount);
    }
}
