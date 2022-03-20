<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Strategies;

use CommissionTask\Components\TransactionFeeCalculator\Strategies\Interfaces\TransactionFeeCalculateStrategy as TransactionFeeCalculateStrategyContract;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\Traits\CommonCalculateOperations;
use CommissionTask\Entities\Transaction;
use CommissionTask\Repositories\Interfaces\TransactionsRepository;
use CommissionTask\Services\Config as ConfigService;
use CommissionTask\Services\Currency as CurrencyService;
use CommissionTask\Services\Date as DateService;
use CommissionTask\Services\Math as MathService;

class WithdrawPrivateStrategy implements TransactionFeeCalculateStrategyContract
{
    use CommonCalculateOperations;

    /**
     * Create a new transaction fee calculator strategy instance for withdraw transactions of private user.
     */
    public function __construct(
        private ConfigService $configService,
        private CurrencyService $currencyService,
        private MathService $mathService,
        private DateService $dateService,
        private TransactionsRepository $transactionsRepository
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function calculateTransactionFee(Transaction $transaction): string
    {
        $influentialTransactions = $this->getInfluentialTransactions($transaction);

        $amountScale = $this->currencyService->getCurrencyScale($transaction->getCurrencyCode());
        $taxableAmountScale = $amountScale + $this->getRoundedOffDigitsNumber();
        $taxableAmount = $this->getTaxableAmount($transaction, $influentialTransactions, $taxableAmountScale);

        $feeAmount = $this->mathService->mul(
            $taxableAmount,
            $this->getFeeRate(),
            $taxableAmountScale
        );

        return $this->ceilAmount($feeAmount, $amountScale);
    }

    /**
     * Get fee rate.
     */
    private function getFeeRate(): string
    {
        return $this->configService->getConfigByName('feeCalculator.feeRateWithdrawPrivate');
    }

    /**
     * Get non-taxable amount.
     */
    private function getNontaxableAmount(): string
    {
        return $this->configService->getConfigByName('feeCalculator.nontaxableAmountWithdrawPrivate');
    }

    /**
     * Get currency code of non-taxable amount.
     */
    private function getNontaxableCurrencyCode(): string
    {
        return $this->configService->getConfigByName('feeCalculator.nontaxableCurrencyCodeWithdrawPrivate');
    }

    /**
     * Get preferential operations number.
     */
    private function getPreferentialOperationsNumber(): int
    {
        return $this->configService->getConfigByName('feeCalculator.preferentialOperationsNumberWithdrawPrivate');
    }

    /**
     * Get transactions affecting the fee calculation.
     *
     * @return Transaction[]
     */
    private function getInfluentialTransactions(Transaction $transaction): array
    {
        $transactionDate = $transaction->getDate();

        return $this->transactionsRepository->getEarlyUserWithdrawTransactionsFromDate(
            $transaction,
            $this->dateService->getStartOfWeek($transactionDate)
        );
    }

    /**
     * Get taxable amount of given transaction.
     *
     * @param Transaction[] $influentialTransactions
     */
    private function getTaxableAmount(
        Transaction $transaction,
        array $influentialTransactions,
        int $taxableAmountScale
    ): string {
        $transactionAmount = $transaction->getAmount();

        // If more than 3 transactions charge commission for the full amount
        if (count($influentialTransactions) >= $this->getPreferentialOperationsNumber()) {
            return $transactionAmount;
        }

        $nontaxableAmountScale = $this->currencyService->getCurrencyScale($this->getNontaxableCurrencyCode())
            + $this->getRoundedOffDigitsNumber();

        // Calculate the non-taxable amount in the currency of the non-taxable limit
        $lostNontaxableAmount = $this->calculateLostNontaxableAmount(
            $influentialTransactions,
            $nontaxableAmountScale
        );

        // If the non-taxable limit is exhausted, charge the commission for the full amount
        if ($this->mathService->comp($lostNontaxableAmount, $this->mathService::ZERO, $nontaxableAmountScale) === $this->mathService::COMP_RESULT_EQ) {
            return $transactionAmount;
        }

        $transactionCurrencyCode = $transaction->getCurrencyCode();

        // Calculate the taxable amount subtracting the non-taxable amount
        return $this->calculateTaxableAmount(
            $transactionAmount,
            $lostNontaxableAmount,
            $transactionCurrencyCode,
            $taxableAmountScale,
            $nontaxableAmountScale
        );
    }

    /**
     * Calculate lost non-taxable amount.
     *
     * @param Transaction[] $influentialTransactions
     */
    private function calculateLostNontaxableAmount(array $influentialTransactions, int $nontaxableAmountScale): string
    {
        $lostNontaxableAmount = $this->getNontaxableAmount();

        // Subtracts the amount of previous transactions for the week from the non-taxable amount
        while (
            $influentialTransactions
            && $this->mathService->comp($lostNontaxableAmount, $this->mathService::ZERO, $nontaxableAmountScale) === $this->mathService::COMP_RESULT_GT
        ) {
            $influentialTransaction = array_shift($influentialTransactions);
            $influentialTransactionAmountAtNontaxableCurrency = $this->currencyService->convertAmountToCurrency(
                $influentialTransaction->getAmount(),
                $influentialTransaction->getCurrencyCode(),
                $this->getNontaxableCurrencyCode(),
                $nontaxableAmountScale
            );
            $lostNontaxableAmount = $this->mathService->sub(
                $lostNontaxableAmount,
                $influentialTransactionAmountAtNontaxableCurrency,
                $nontaxableAmountScale
            );
        }

        return $this->mathService->max($lostNontaxableAmount, $this->mathService::ZERO, $nontaxableAmountScale);
    }

    /**
     * Calculate the taxable amount subtracting the non-taxable amount.
     */
    private function calculateTaxableAmount(
        string $transactionAmount,
        string $lostNontaxableAmount,
        string $transactionCurrencyCode,
        int $taxableAmountScale,
        int $nontaxableAmountScale
    ): string {
        $nontaxableCurrencyCode = $this->getNontaxableCurrencyCode();

        // Convert the transaction amount to the currency of the non-taxable limit
        $transactionAmountAtNontaxableCurrency = $this->currencyService->convertAmountToCurrency(
            $transactionAmount,
            $transactionCurrencyCode,
            $nontaxableCurrencyCode,
            $nontaxableAmountScale
        );

        // Calculate the part of the transaction amount taxed by the commission in the currency of the non-taxable limit
        $taxableAmountAtNontaxableCurrency = $this->mathService->sub(
            $transactionAmountAtNontaxableCurrency,
            $lostNontaxableAmount,
            $nontaxableAmountScale
        );
        $taxableAmountAtNontaxableCurrency = $this->mathService->max(
            $taxableAmountAtNontaxableCurrency,
            $this->mathService::ZERO,
            $nontaxableAmountScale
        );

        // Convert the taxable amount to the currency of the transaction
        $taxableAmount = $this->currencyService->convertAmountToCurrency(
            $taxableAmountAtNontaxableCurrency,
            $nontaxableCurrencyCode,
            $transactionCurrencyCode,
            $taxableAmountScale
        );

        $ceilScale = $taxableAmountScale - $this->getRoundedOffDigitsNumber();

        // Round up the taxable amount and return it
        return $this->ceilAmount($taxableAmount, $ceilScale);
    }
}
