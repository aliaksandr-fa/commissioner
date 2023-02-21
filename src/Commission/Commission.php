<?php declare(strict_types=1);

namespace Commissioner\Commission;


/**
 * Interface Commission
 *
 * @package Commissioner\Commission
 */
interface Commission
{
    public function forCountry(string $countryCode): ?float;
}