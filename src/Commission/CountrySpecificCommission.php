<?php declare(strict_types=1);

namespace Commissioner\Commission;

/**
 * Class CountrySpecificCommission
 *
 * @package Commissioner\Commission
 */
class CountrySpecificCommission implements Commission
{
    public function __construct(
        private readonly array $countriesCodes,
        private readonly float $commission,
        private readonly Commission $next
    ) {}

    private function hasCommission(string $countryCode): bool
    {
        return in_array(strtoupper($countryCode), $this->countriesCodes);
    }

    public function forCountry(string $countryCode): ?float
    {
        if ($this->hasCommission($countryCode))
        {
            return $this->commission;
        }

        return $this->next->forCountry($countryCode);
    }
}
