<?php declare(strict_types=1);

namespace Commissioner\Tests\Unit\BinNumber;

use Commissioner\BinNumber\BinNumber;
use Commissioner\BinNumber\BinNumberCountryResolver;
use Commissioner\BinNumber\CacheableBinNumberCountryResolver;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\ItemInterface;


/**
 * Class CacheableBinNumberCountryResolverTest
 *
 * @package Commissioner\Tests\Unit\Exchange
 */
class CacheableBinNumberCountryResolverTest extends TestCase
{
    /**
     * @test
     */
    public function Should_ReturnFreshCountryCode_When_CacheIsEmpty()
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

        $decoratedResolver = $this->createMock(BinNumberCountryResolver::class);
        $decoratedResolver
            ->method('resolveCountry')
            ->willReturn('DK')
        ;

        $resolver = new CacheableBinNumberCountryResolver($decoratedResolver, $cache);

        $this->assertEquals('DK', $resolver->resolveCountry(new BinNumber('11111')));
    }

    /**
     * @test
     */
    public function Should_ReturnCachedCountryCode_When_ValueWasPreviouslyCached()
    {
        $cacheItem = $this->createMock(ItemInterface::class);
        $cacheItem
            ->method('get')
            ->willReturn('PO')
        ;
        $cache   = $this->createMock(CacheItemPoolInterface::class);
        $cache
            ->method('getItem')
            ->willReturn($cacheItem)
        ;

        $decoratedResolver = $this->createMock(BinNumberCountryResolver::class);
        $decoratedResolver
            ->method('resolveCountry')
            ->willReturn('DK')
        ;

        $resolver = new CacheableBinNumberCountryResolver($decoratedResolver, $cache);

        $this->assertEquals('PO', $resolver->resolveCountry(new BinNumber('11111')));
    }
}
