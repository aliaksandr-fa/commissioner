<?php declare(strict_types=1);

namespace Commissioner\Tests\Unit\Exchange;


use Commissioner\Exchange\CacheableExchangeRateProvider;
use Commissioner\Exchange\ExchangeRateProvider;
use Commissioner\Transaction\Currency;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class CacheableExchangeRateProviderTest
 *
 * @package Commissioner\Tests\Unit\Exchange
 */
class CacheableExchangeRateProviderTest extends TestCase
{
    /**
     * @test
     */
    public function Should_ReturnFreshRate_When_CacheIsEmpty()
    {
        $cacheItem = $this->createMock(ItemInterface::class);
        $cacheItem
            ->method('get')
            ->willReturn(null)
        ;
        $cache   = $this->createMock(CacheItemPoolInterface::class);
        $cache
            ->method('getItem')
            ->willReturn($cacheItem)
        ;

        $decoratedRatesProvider = $this->createMock(ExchangeRateProvider::class);
        $decoratedRatesProvider
            ->method('getRate')
            ->willReturn(123.0)
        ;

        $provider = new CacheableExchangeRateProvider($decoratedRatesProvider, $cache);

        $this->assertEquals(123, $provider->getRate(new Currency('EUR'), new Currency('USD')));
    }

    /**
     * @test
     */
    public function Should_ReturnCachedValue_When_ValueWasPreviouslyCached()
    {
        $cacheItem = $this->createMock(ItemInterface::class);
        $cacheItem
            ->method('get')
            ->willReturn(321.0)
        ;
        $cache   = $this->createMock(CacheItemPoolInterface::class);
        $cache
            ->method('getItem')
            ->willReturn($cacheItem)
        ;

        $decoratedRatesProvider = $this->createMock(ExchangeRateProvider::class);
        $decoratedRatesProvider
            ->method('getRate')
            ->willReturn(666.0)
        ;

        $provider = new CacheableExchangeRateProvider($decoratedRatesProvider, $cache);

        $this->assertEquals(321, $provider->getRate(new Currency('EUR'), new Currency('USD')));
    }
}
