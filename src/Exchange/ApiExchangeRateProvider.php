<?php declare(strict_types=1);

namespace Commissioner\Exchange;

use Commissioner\Transaction\Currency;
use Symfony\Contracts\HttpClient\HttpClientInterface;


/**
 * Class ApiExchangeRatesProvider
 *
 */
class ApiExchangeRateProvider implements ExchangeRateProvider
{
    public function __construct(
        private HttpClientInterface $client,
        private string $apiUrl,
        private string $apiKey
    ) {}

    public function getRate(Currency $base, Currency $to): float
    {
        try {

            $response = $this->client->request('GET', $this->apiUrl, [
                'headers' => ['apiKey' => $this->apiKey],
                'timeout' => 5
            ]);

            $data = $response->toArray();

        }
        catch (\Throwable $exception)
        {
            throw new ExchangeRateProviderException("Failed to get exchange rates: {$exception->getMessage()}");
        }

        $rate = $data['rates'][$to->code()] ?? throw new ExchangeRateProviderException("No exchange rates for this currency pair.");

        return floatval($rate);
    }
}
