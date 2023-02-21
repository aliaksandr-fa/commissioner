<?php declare(strict_types=1);

namespace Commissioner\Exchange;

use Commissioner\Transaction\Currency;
use Psr\Cache\CacheItemPoolInterface;


/**
 * Class CacheableExchangeRateProvider
 *
 * @package Commissioner\Exchange
 */
class CacheableExchangeRateProvider implements ExchangeRateProvider
{
    private const RATES_CACHE_KEY_PATTERN = "commissioner.rate_%s_%s";

    public function __construct(
        private readonly ExchangeRateProvider $provider,
        private readonly CacheItemPoolInterface $cache,
        private readonly int $ttl = 5) {}

    public function getRate(Currency $base, Currency $to): float
    {
        $key = sprintf(self::RATES_CACHE_KEY_PATTERN, $base->code(), $to->code());

        $cached = $this->cache->getItem($key);

        if ($cached->get() === null)
        {
            $rate = $this->provider->getRate($base, $to);

            $cached->set($rate);
            $cached->expiresAfter($this->ttl);

            $this->cache->save($cached);

            return $rate;
        }

        return $cached->get();
    }
}
