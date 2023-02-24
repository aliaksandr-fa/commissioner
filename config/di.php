<?php

use Commissioner\BinNumber\ApiBinNumberCountryResolver;
use Commissioner\BinNumber\BinNumberCountryResolver;
use Commissioner\BinNumber\CacheableBinNumberCountryResolver;
use Commissioner\CLI\Input;
use Commissioner\Commission\Commission;
use Commissioner\Commission\CountrySpecificCommission;
use Commissioner\Commission\DefaultCommission;
use Commissioner\Exchange\ApiExchangeRateProvider;
use Commissioner\Exchange\CacheableExchangeRateProvider;
use Commissioner\Exchange\ExchangeRateProvider;
use Commissioner\Transaction\Presenter\ConsoleCommissionsPresenter;
use Commissioner\Transaction\Repository\PlainFileTransactionsRepository;
use Commissioner\Transaction\Repository\TransactionsRepository;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;


$container = new DI\Container([

    'db.host' => 'localhost',
    'db.port' => 3336,
    'bin.api_url' => getenv('BIN_NUMBER_API_URL'),
    'bin.api_cache_ttl' => (int)getenv('BIN_NUMBER_API_CACHE_TTL') ?? null,
    'exchange.api_url' => getenv('CURRENCY_EXCHANGE_API_URL'),
    'exchange.api_key' => getenv('CURRENCY_EXCHANGE_API_KEY'),
    'exchange.api_cache_ttl' => (int)getenv('CURRENCY_EXCHANGE_CACHE_TTL') ?? null,

    'eu_country_codes' => explode(',', getenv('EU_COUNTRIES')),
    'commission_rate_eu' => (float)getenv('COMMISSION_EU'),
    'commission_rate_default' => (float)getenv('COMMISSION_DEFAULT'),

    Input::class => new Input($argv),

    HttpClientInterface::class             => DI\factory(fn () => HttpClient::create()),
    TransactionsRepository::class          => DI\get(PlainFileTransactionsRepository::class),
    PlainFileTransactionsRepository::class => DI\factory(function (ContainerInterface $container)
    {
        /** @var Input $input */
        $input = $container->get(Input::class);

        return new PlainFileTransactionsRepository($input->getInputFilePath());
    }),

    CacheItemPoolInterface::class => DI\factory(function (ContainerInterface $container)
    {
        return new ArrayAdapter();
    }),

    BinNumberCountryResolver::class => DI\factory(function (ContainerInterface $container)
    {
        $resolverToDecorate = $container->get(ApiBinNumberCountryResolver::class);

        return new CacheableBinNumberCountryResolver(
            $resolverToDecorate,
            $container->get(CacheItemPoolInterface::class),
            $container->get('bin.api_cache_ttl')
        );
    }),

    ApiBinNumberCountryResolver::class => DI\create()->constructor(
        DI\get(HttpClientInterface::class),
        DI\get('bin.api_url')
    ),

    ExchangeRateProvider::class => DI\factory(function (ContainerInterface $container)
    {
        $resolverToDecorate = $container->get(ApiExchangeRateProvider::class);

        return new CacheableExchangeRateProvider(
            $resolverToDecorate,
            $container->get(CacheItemPoolInterface::class),
            $container->get('exchange.api_cache_ttl')
        );
    }),


    ApiExchangeRateProvider::class => DI\create()->constructor(
        DI\get(HttpClientInterface::class),
        DI\get('exchange.api_url'),
        DI\get('exchange.api_key'),
    ),

    Commission::class => DI\factory(function (ContainerInterface $container)
    {
        return new CountrySpecificCommission(
            $container->get('eu_country_codes'),
            $container->get('commission_rate_eu'),
            new DefaultCommission($container->get('commission_rate_default'))
        );
    }),

    ConsoleCommissionsPresenter::class => new ConsoleCommissionsPresenter()
]);

return $container;
