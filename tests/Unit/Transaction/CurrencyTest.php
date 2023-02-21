<?php declare(strict_types=1);

namespace Commissioner\Tests\Unit\Transaction;

use Commissioner\Transaction\Currency;
use PHPUnit\Framework\TestCase;

/**
 * Class CurrencyTest
 *
 * @package Commissioner\Tests\Unit\Transaction
 */
class CurrencyTest extends TestCase
{
    /**
     * @test
     */
    public function Should_MakeUpperCaseCurrencyCode_When_ProvidedWithCurrencyCode()
    {
        $this->assertEquals('EUR', (new Currency('EUR'))->code());
        $this->assertEquals('EUR', (new Currency('eur'))->code());
    }

    /**
     * @test
     * @dataProvider  getInvalidCurrencyCodeProvider
     */
    public function Should_ThrowException_When_InvalidCurrencyCodeProvided(string $code)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid currency code.");

        new Currency($code);
    }


    public function getInvalidCurrencyCodeProvider(): array
    {
        return [
            [''],
            ['1'],
            ['11'],
            ['111'],
            ['1111']
        ];
    }
}
