<?php declare(strict_types=1);

namespace Commissioner\BinNumber;


/**
 * Class BinNumberCountryResolver
 *
 */
interface BinNumberCountryResolver
{
    /**
     * @throws BinNumberCountryResolverException
     *
     * @param BinNumber $bin
     * @return string|null
     */
    public function resolveCountry(BinNumber $bin): ?string;
}
