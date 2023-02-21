<?php declare(strict_types=1);

namespace Commissioner\Tests\Unit\Commission;

use Commissioner\Commission\CountrySpecificCommission;
use Commissioner\Commission\DefaultCommission;
use PHPUnit\Framework\TestCase;


/**
 * Class CountrySpecificCommissionTest
 *
 * @package Commissioner\Tests\Unit\Commission
 */
class CountrySpecificCommissionTest extends TestCase
{
    private DefaultCommission $defaultCommission;

    public function setUp(): void
    {
        $this->defaultCommission = new DefaultCommission(0.05);
    }

    /**
     * @test
     * @dataProvider getCountriesWithSpecificCommissionDataProvider
     */
    public function Should_ReturnSpecificCommission_When_EligibleCountryCodeProvided(array $countries, $country, $rate)
    {
        $euRate = 0.03;

        $commission = new CountrySpecificCommission($countries, $euRate, $this->defaultCommission);

        $this->assertEquals($rate, $commission->forCountry($country));
    }

    /**
     * @test
     * @dataProvider getCountriesWithSpecificCommissionDataProvider
     */
    public function Should_ReturnDefaultCommission_When_NonEligibleCountryCodeProvided(array $countries, $country, $rate)
    {
        $euRate = 0.03;

        $commission = new CountrySpecificCommission($countries, $euRate, $this->defaultCommission);

        $this->assertEquals($rate, $commission->forCountry($country));
    }

    public function getCountriesWithSpecificCommissionDataProvider(): array
    {
        return [
            [['AT','BE','BG'], 'AT', 0.03],
            [['AT','BE','BG'], 'BE', 0.03],
            [['AT','BE','BG'], 'BG', 0.03]
        ];
    }

    public function getCountriesWithoutSpecificCommissionDataProvider(): array
    {
        return [
            [['AT','BE','BG'], 'US', 0.05],
            [[], 'AT', 0.05]
        ];
    }
}
