<?php declare(strict_types=1);

namespace Commissioner\Commission;


/**
 * Class DefaultCommission
 *
 * @package Commissioner\Commission
 */
class DefaultCommission implements Commission
{
    public function __construct(private readonly float $value) {}

    public function forCountry(string $countryCode): ?float
    {
        return $this->value;
    }
}
