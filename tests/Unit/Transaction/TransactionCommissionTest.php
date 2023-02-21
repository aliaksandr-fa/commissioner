<?php declare(strict_types=1);

namespace Commissioner\Tests\Unit\Transaction;

use Commissioner\Transaction\Currency;
use Commissioner\Transaction\TransactionCommission;
use PHPUnit\Framework\TestCase;


/**
 * Class TransactionCommissionTest
 *
 * @package Commissioner\Tests\Unit\Transaction
 */
class TransactionCommissionTest extends TestCase
{
    /**
     * @test
     */
    public function Should_ThrowExceptions_When_TryingToCeilWithnegativePrecision()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Precision should be positive.');

        $commission = new TransactionCommission(99.99, new Currency('EUR'));
        $commission->ceil(-1);
    }

    /**
     * @test
     * @dataProvider getCommissionsToCeilDataProvider
     */
    public function Should_CeilCorrectly_When_CeilPrecisionIsPositive(float $commission, int $precision, float $rounded)
    {
        $commission = new TransactionCommission($commission, new Currency('EUR'));

        $this->assertEquals($rounded, $commission->ceil($precision));
    }

    public function getCommissionsToCeilDataProvider(): array
    {
        return [
            [99.998, 2, 100.0],
            [99.998, 2, 100],
            [99.998, 1, 100],
            [99.998, 0, 100],
            [100.551, 2, 100.56],
            [100.55, 2, 100.55],
        ];
    }
}
