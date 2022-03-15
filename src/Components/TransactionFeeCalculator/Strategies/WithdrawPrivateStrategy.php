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
        private TransactionsRepository $transactionsRepository,
        private DateService $dateService,
        private CurrencyService $currencyService
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function calculateTransactionFee(Transaction $transaction): string
    {
        $amount = $transaction->getAmount();
        $mathService = new MathService(
            $this->determineScaleOfAmount($amount) + $this->getRoundedOffDigitsNumber()
        );

        $influentialTransactions = $this->getInfluentialTransactions($transaction);

        $taxableAmount = $this->getTaxableAmount($transaction, $influentialTransactions, $mathService);

        $feeAmount = $mathService->mul(
            $taxableAmount,
            ConfigService::getConfigByName('feeCalculator.feeRateWithdrawPrivate')
        );

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

        $influentialTransactionsFilterMethod = fn (Transaction $checkedTransaction) => $checkedTransaction->getUserId() === $transactionUserId
            && $checkedTransaction->getDate() >= $transactionDateStartOfWeek
            && $checkedTransaction->getDate() < $transactionDate
            && $checkedTransaction->getType() === $transactionType
            && $checkedTransaction->getUserType() === $transactionUserType;

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
        MathService $transactionMathService
    ): string {
        $transactionAmount = $transaction->getAmount();

        // If more than 3 transactions charge commission for the full amount
        if (
            count($influentialTransactions)
            >= ConfigService::getConfigByName('feeCalculator.preferentialOperationsNumberWithdrawPrivate')
        ) {
            return $transactionAmount;
        }

        $nontaxableAmountMathService = new MathService(
            $this->determineScaleOfAmount(ConfigService::getConfigByName('feeCalculator.nontaxableAmountWithdrawPrivate'))
            + $this->getRoundedOffDigitsNumber()
        );

        // Calculate the non-taxable amount in the currency of the non-taxable limit
        $lostNontaxableAmount = $this->calculateLostNontaxableAmount(
            $influentialTransactions,
            $nontaxableAmountMathService
        );

        // If the non-taxable limit is exhausted, charge the commission for the full amount
        if (
            $nontaxableAmountMathService->comp($lostNontaxableAmount, $nontaxableAmountMathService::ZERO)
            === $nontaxableAmountMathService::COMP_RESULT_EQ
        ) {
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
        MathService $nontaxableAmountMathService
    ): string {
        $lostNontaxableAmount = ConfigService::getConfigByName('feeCalculator.nontaxableAmountWithdrawPrivate');

        // Subtracts the amount of previous transactions for the week from the non-taxable amount
        while ($influentialTransactions
            && $nontaxableAmountMathService->comp($lostNontaxableAmount, $nontaxableAmountMathService::ZERO)
                === $nontaxableAmountMathService::COMP_RESULT_GT) {
            $influentialTransaction = array_shift($influentialTransactions);
            $influentialTransactionAmountAtNontaxableCurrency = $this->currencyService->convertAmountToCurrency(
                $influentialTransaction->getAmount(),
                $influentialTransaction->getCurrencyCode(),
                ConfigService::getConfigByName('feeCalculator.nontaxableCurrencyCodeWithdrawPrivate'),
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
        MathService $transactionMathService,
        MathService $nontaxableAmountMathService
    ): string {
        $nontaxableCurrencyCode = ConfigService::getConfigByName('feeCalculator.nontaxableCurrencyCodeWithdrawPrivate');

        // Convert the transaction amount to the currency of the non-taxable limit
        $transactionAmountAtNontaxableCurrency = $this->currencyService->convertAmountToCurrency(
            $transactionAmount,
            $transactionCurrencyCode,
            $nontaxableCurrencyCode,
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
            $nontaxableCurrencyCode,
            $transactionCurrencyCode,
            $transactionMathService
        );

        // Round up the taxable amount and return it
        return $this->ceilAmount($taxableAmount);
    }
}
