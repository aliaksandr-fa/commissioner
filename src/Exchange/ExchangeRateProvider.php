<?php declare(strict_types=1);

namespace Commissioner\Exchange;

use Commissioner\Transaction\Currency;


/**
 * Interface ExchangeRateProvider
 *
 * @package Commissioner\Exchange
 */
interface ExchangeRateProvider
{
    /**
     * @throws ExchangeRateProviderException
     *
     * @param Currency $base
     * @param Currency $to
     * @return float
     */
    public function getRate(Currency $base, Currency $to): float;
}
