<?php declare(strict_types=1);

namespace Commissioner\Tests\Unit\Commission;

use Commissioner\Commission\DefaultCommission;
use Commissioner\Transaction\Currency;
use PHPUnit\Framework\TestCase;

/**
 * Class DefaultCommission
 *
 * @package Commissioner\Tests\Unit\Commission
 */
class DefaultCommissionTest extends TestCase
{
    /**
     * @test
     */
    public function Should_ReturnDefaultCommission_When_AnyCountryCodeProvided()
    {
        $defaultCommission = 0.666;

        $commission = new DefaultCommission($defaultCommission);

        $this->assertEquals($defaultCommission, $commission->forCountry('EU'));
        $this->assertEquals($defaultCommission, $commission->forCountry('US'));
        $this->assertEquals($defaultCommission, $commission->forCountry('UK'));
    }
}
