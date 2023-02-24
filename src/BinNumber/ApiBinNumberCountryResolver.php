<?php declare(strict_types=1);

namespace Commissioner\BinNumber;

use Symfony\Contracts\HttpClient\HttpClientInterface;


/**
 * Class ApiBinNumberCountryResolver
 *
 * @package Commissioner\BinNumber
 */
class ApiBinNumberCountryResolver implements BinNumberCountryResolver
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $apiUrl
    ) {}

    public function resolveCountry(BinNumber $bin): ?string
    {
        try {

            $url = "{$this->apiUrl}/{$bin->value()}";

            $response = $this->client->request('GET', $url, ['timeout' => 5]);

            $data = $response->toArray();

        }
        catch (\Throwable $exception)
        {
            throw new BinNumberCountryResolverException("Failed to get exchange rates: {$exception->getMessage()}");
        }

        return $data['country']['alpha2'] ?? throw new BinNumberCountryResolverException("Bin number not found.");
    }
}
