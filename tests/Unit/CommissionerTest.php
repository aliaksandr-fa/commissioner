<?php declare(strict_types=1);

namespace Commissioner\Tests\Unit;

use Commissioner\BinNumber\ApiBinNumberCountryResolver;
use Commissioner\BinNumber\BinNumber;
use Commissioner\BinNumber\BinNumberCountryResolver;
use Commissioner\BinNumber\BinNumberCountryResolverException;
use Commissioner\Commission\Commission;
use Commissioner\Commissioner;
use Commissioner\Exchange\ApiExchangeRateProvider;
use Commissioner\Exchange\ExchangeRateProvider;
use Commissioner\Exchange\ExchangeRateProviderException;
use Commissioner\Transaction\Currency;
use Commissioner\Transaction\Repository\TransactionsRepository;
use Commissioner\Transaction\Transaction;
use Commissioner\Transaction\TransactionCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;


/**
 * Class CommissionerTest
 *
 * @package Commissioner\Tests\Unit
 */
class CommissionerTest extends TestCase
{
    private TransactionsRepository $transactionsRepository;
    private BinNumberCountryResolver $binNumberCountryResolver;
    private ExchangeRateProvider $exchangeRateProvider;
    private Commission $commissions;

    public function setUp(): void
    {
        $this->transactionsRepository   = $this->createMock(TransactionsRepository::class);
        $this->binNumberCountryResolver = $this->createMock(BinNumberCountryResolver::class);
        $this->exchangeRateProvider     = $this->createMock(ExchangeRateProvider::class);
        $this->commissions              = $this->createMock(Commission::class);

        $this->transactionsRepository
            ->method('getTransactionsToProcess')
            ->willReturn(
                new TransactionCollection(
                    new Transaction(new BinNumber('1111'), 100, new Currency('EUR'))
                )
            )
        ;

        $this->binNumberCountryResolver
            ->method('resolveCountry')
            ->willReturn('DE')
        ;

        $this->exchangeRateProvider
            ->method('getRate')
            ->willReturn(0.0)
        ;

        $this->commissions
            ->method('forCountry')
            ->willReturn(0.02)
        ;
    }

    /**
     * @test
     */
    public function Should_SkipCurrencyExchange_When_BaseAndTransactionCurrenciesAreTheSame()
    {
        $commissioner = new Commissioner(
            $this->transactionsRepository,
            $this->binNumberCountryResolver,
            $this->exchangeRateProvider,
            $this->commissions
        );

        $commissions = $commissioner->charge();

        $this->assertCount(1, $commissions);
        $this->assertEquals('EUR', $commissions[0]->currency()->code());
        $this->assertEquals(2, $commissions[0]->value());
    }

    /**
     * @test
     */
    public function Should_NotApplyExchangeRate_When_ExchangeRateIsLessOrEqualThenZero()
    {
        $this->transactionsRepository = $this->createMock(TransactionsRepository::class);
        $this->transactionsRepository
            ->method('getTransactionsToProcess')
            ->willReturn(
                new TransactionCollection(
                    new Transaction(new BinNumber('1111'), 100, new Currency('USD'))
                )
            )
        ;

        $commissioner = new Commissioner(
            $this->transactionsRepository,
            $this->binNumberCountryResolver,
            $this->exchangeRateProvider,
            $this->commissions
        );

        $commissions = $commissioner->charge();

        $this->assertCount(1, $commissions);
        $this->assertEquals('EUR', $commissions[0]->currency()->code());
        $this->assertEquals(2, $commissions[0]->value());
    }


    /**
     * @test
     */
    public function Should_ApplyExchangeRate_When_BaseAndTransactionCurrenciesDiffer()
    {
        $this->transactionsRepository = $this->createMock(TransactionsRepository::class);
        $this->transactionsRepository
            ->method('getTransactionsToProcess')
            ->willReturn(
                new TransactionCollection(
                    new Transaction(new BinNumber('1111'), 100, new Currency('USD'))
                )
            )
        ;


        $this->commissions = $this->createMock(Commission::class);
        $this->commissions
            ->method('forCountry')
            ->willReturn(1.0)
        ;

        $this->exchangeRateProvider = $this->createMock(ExchangeRateProvider::class);
        $this->exchangeRateProvider
            ->method('getRate')
            ->willReturn(1.1)
        ;

        $commissioner = new Commissioner(
            $this->transactionsRepository,
            $this->binNumberCountryResolver,
            $this->exchangeRateProvider,
            $this->commissions
        );

        $commissions = $commissioner->charge();

        $this->assertCount(1, $commissions);
        $this->assertEquals('EUR', $commissions[0]->currency()->code());
        $this->assertEquals(90.91, $commissions[0]->ceil(2));
    }

    /**
     * @test
     * @return void
     */
    public function Should_ThrowException_When_RequestToBinNumberApiFailed(): void
    {
        $this->expectException(BinNumberCountryResolverException::class);
        $this->expectExceptionMessageMatches("/^Failed to get exchange rates.*/");

        $http = new MockHttpClient([
            new MockResponse(json_encode([]), ['http_code' => 500])
        ]);

        $this->binNumberCountryResolver = new ApiBinNumberCountryResolver($http, '');

        $commissioner = new Commissioner(
            $this->transactionsRepository,
            $this->binNumberCountryResolver,
            $this->exchangeRateProvider,
            $this->commissions
        );

        $commissioner->charge();
    }

    /**
     * @test
     * @return void
     */
    public function Should_ThrowException_When_BinNumberApiDidntReturnBinNumber(): void
    {
        $this->expectException(BinNumberCountryResolverException::class);
        $this->expectExceptionMessage("Bin number not found.");

        $http = new MockHttpClient([
            new MockResponse(json_encode(['test' => true]), ['http_code' => 200])
        ]);

        $this->binNumberCountryResolver = new ApiBinNumberCountryResolver($http, '');

        $commissioner = new Commissioner(
            $this->transactionsRepository,
            $this->binNumberCountryResolver,
            $this->exchangeRateProvider,
            $this->commissions
        );

        $commissioner->charge();
    }

    /**
     * @test
     * @return void
     */
    public function Should_ThrowException_When_RequestToExchangeApiFailed(): void
    {
        $this->expectException(ExchangeRateProviderException::class);
        $this->expectExceptionMessageMatches("/^Failed to get exchange rates: .*/");

        $http = new MockHttpClient([
            new MockResponse(json_encode([]), ['http_code' => 500])
        ]);

        $this->transactionsRepository = $this->createMock(TransactionsRepository::class);
        $this->transactionsRepository
            ->method('getTransactionsToProcess')
            ->willReturn(
                new TransactionCollection(
                    new Transaction(new BinNumber('1111'), 100, new Currency('USD'))
                )
            )
        ;
        $this->exchangeRateProvider = new ApiExchangeRateProvider($http, '', '');

        $commissioner = new Commissioner(
            $this->transactionsRepository,
            $this->binNumberCountryResolver,
            $this->exchangeRateProvider,
            $this->commissions
        );

        $commissioner->charge();
    }

    /**
     * @test
     * @return void
     */
    public function Should_ThrowException_When_ExchangeRateApiDidntReturnRate(): void
    {
        $this->expectException(ExchangeRateProviderException::class);
        $this->expectExceptionMessage("No exchange rates for this currency pair.");

        $http = new MockHttpClient([
            new MockResponse(json_encode(['test' => true]), ['http_code' => 200])
        ]);

        $this->transactionsRepository = $this->createMock(TransactionsRepository::class);
        $this->transactionsRepository
            ->method('getTransactionsToProcess')
            ->willReturn(
                new TransactionCollection(
                    new Transaction(new BinNumber('1111'), 100, new Currency('USD'))
                )
            )
        ;
        $this->exchangeRateProvider = new ApiExchangeRateProvider($http, '', '');

        $commissioner = new Commissioner(
            $this->transactionsRepository,
            $this->binNumberCountryResolver,
            $this->exchangeRateProvider,
            $this->commissions
        );

        $commissioner->charge();
    }
}
