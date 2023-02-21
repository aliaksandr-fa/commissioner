<?php declare(strict_types=1);

namespace Commissioner\Tests\Unit\Transaction;

use Commissioner\BinNumber\BinNumber;
use Commissioner\Transaction\Currency;
use Commissioner\Transaction\Transaction;
use PHPUnit\Framework\TestCase;


/**
 * Class TransactionTest
 *
 * @package Commissioner\Tests\Unit\Transaction
 */
class TransactionTest extends TestCase
{
    /**
     * @test
     * @dataProvider getTransactionsAndCurrenciesDataProvider
     */
    public function Should_CheckCurrencyCorrectly_When_CurrencyCheckedIfTransactionOfParticularCurrency(Transaction $transaction, Currency $currency, bool $shouldBeEqual)
    {
        $this->assertEquals($shouldBeEqual, $transaction->ofCurrency($currency));
    }

    public function getTransactionsAndCurrenciesDataProvider(): array
    {
        return [
            [new Transaction(new BinNumber('0000'), 0, new Currency('EUR')), new Currency('EUR'), true],
            [new Transaction(new BinNumber('0000'), 0, new Currency('EUR')), new Currency('eur'), true],
            [new Transaction(new BinNumber('0000'), 0, new Currency('eur')), new Currency('EUR'), true],
            [new Transaction(new BinNumber('0000'), 0, new Currency('EUR')), new Currency('USD'), false],
            [new Transaction(new BinNumber('0000'), 0, new Currency('USD')), new Currency('EUR'), false]
        ];
    }
}
