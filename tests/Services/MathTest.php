<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Services;

use CommissionTask\Services\Math as MathService;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass MathService
 */
final class MathTest extends TestCase
{
    /**
     * @var MathService
     */
    private $mathService;

    protected function setUp(): void
    {
        $this->mathService = new MathService(2);
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @param int $expectation
     *
     * @dataProvider dataProviderForCompTesting
     */
    public function testComp(string $leftOperand, string $rightOperand, int $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->mathService->comp($leftOperand, $rightOperand)
        );
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @param string $expectation
     *
     * @dataProvider dataProviderForAddTesting
     */
    public function testAdd(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->mathService->add($leftOperand, $rightOperand)
        );
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @param string $expectation
     *
     * @dataProvider dataProviderForSubTesting
     */
    public function testSub(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->mathService->sub($leftOperand, $rightOperand)
        );
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @param string $expectation
     *
     * @dataProvider dataProviderForMulTesting
     */
    public function testMul(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->mathService->mul($leftOperand, $rightOperand)
        );
    }

    /**
     * @param string $degreeBase
     * @param string $exponent
     * @param string $expectation
     *
     * @dataProvider dataProviderForPowTesting
     */
    public function testPow(string $degreeBase, string $exponent, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->mathService->pow($degreeBase, $exponent)
        );
    }

    /**
     * @param string[] $operands
     * @param string $expectation
     *
     * @dataProvider dataProviderForMaxTesting
     */
    public function testMax(array $operands, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            call_user_func_array([$this->mathService, 'max'], $operands)
        );
    }

    public function dataProviderForCompTesting(): array
    {
        return [
            'compare a smaller natural number to a larger one' => ['1', '2', MathService::COMP_RESULT_LT],
            'compare a smaller natural number to a larger float' => ['1', '1.05', MathService::COMP_RESULT_LT],
            'compare equal natural numbers' => ['5', '5', MathService::COMP_RESULT_EQ],
            'compare equal float numbers' => ['3.14', '3.14', MathService::COMP_RESULT_EQ],
            'compare a larger float number with a smaller float number of insufficient scale' => ['2.00', '2.005', MathService::COMP_RESULT_EQ],
            'compare a larger natural number to a smaller one' => ['5', '3', MathService::COMP_RESULT_GT],
            'compare a larger float number to a smaller natural' => ['3.50', '3', MathService::COMP_RESULT_GT],
        ];
    }

    public function dataProviderForAddTesting(): array
    {
        return [
            'add 2 natural numbers' => ['1', '2', '3.00'],
            'add negative number to a positive' => ['-1', '2', '1.00'],
            'add natural number to a float' => ['1', '1.05123', '2.05'],
        ];
    }

    public function dataProviderForSubTesting(): array
    {
        return [
            'subtract one natural number from another' => ['8', '5', '3.00'],
            'subtract a negative number from a positive' => ['5', '-2', '7.00'],
            'subtract a positive number from a negative' => ['-5', '2', '-7.00'],
            'subtract a natural number from a float' => ['7.33', '5', '2.33'],
            'subtract a float number from a natural' => ['7', '2.33', '4.67'],
        ];
    }

    public function dataProviderForMulTesting(): array
    {
        return [
            'multiple 2 natural numbers' => ['5', '2', '10.00'],
            'multiply a negative number by a positive number' => ['-5', '8', '-40.00'],
            'multiply 2 negative numbers' => ['-6', '-4', '24.00'],
            'multiply a natural number to a float' => ['4', '1.054', '4.21'],
        ];
    }

    public function dataProviderForPowTesting(): array
    {
        return [
            'raise a natural number to the power given by the natural number' => ['3', '3', '27.00'],
            'raise a natural number to the power given by the negative number' => ['3', '-1', '0.33'],
            'raise a float number to the power given by the natural number' => ['0.3', '2', '0.09'],
            'raise a float number to the power given by the negative number' => ['0.5', '-2', '4.00'],
        ];
    }

    public function dataProviderForMaxTesting(): array
    {
        return [
            'find the maximum value where the first number is greater than the second' => [['1.25', '1.19'], '1.25'],
            'find the maximum value where the second number is greater than the first' => [['1.19', '1.25'], '1.25'],
            'find the maximal number among equal numbers' => [['1.19', '1.19'], '1.19'],
            'find the maximal number among three numbers' => [['-5.5', '3', '3.01'], '3.01'],
        ];
    }
}
