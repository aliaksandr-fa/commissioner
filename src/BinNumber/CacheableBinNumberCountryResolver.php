<?php declare(strict_types=1);

namespace Commissioner\BinNumber;

use Psr\Cache\CacheItemPoolInterface;


/**
 * Class CacheableBinNumberCountryResolver
 *
 * @package Commissioner\BinNumber
 */
class CacheableBinNumberCountryResolver implements BinNumberCountryResolver
{
    private const BIN_NUMBER_CACHE_KEY_PATTERN = "commissioner.bin_%s";

    public function __construct(
        private readonly BinNumberCountryResolver $resolver,
        private readonly CacheItemPoolInterface $cache,
        private readonly int $ttl = 5) {}

    public function resolveCountry(BinNumber $bin): ?string
    {
        $key = sprintf(self::BIN_NUMBER_CACHE_KEY_PATTERN, $bin->value());

        $cached = $this->cache->getItem($key);

        if ($cached->get() === null)
        {
            $countryCode = $this->resolver->resolveCountry($bin);

            $cached->set($countryCode);
            $cached->expiresAfter($this->ttl);

            $this->cache->save($cached);

            return $countryCode;
        }

        return $cached->get();
    }
}
