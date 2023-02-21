<?php

use Commissioner\BinNumber\ApiBinNumberCountryResolver;
use Commissioner\BinNumber\CacheableBinNumberCountryResolver;
use Commissioner\CLI\Input;
use Commissioner\Commission\CountrySpecificCommission;
use Commissioner\Commission\DefaultCommission;
use Commissioner\Commissioner;
use Commissioner\Exchange\ApiExchangeRateProvider;
use Commissioner\Exchange\CacheableExchangeRateProvider;
use Commissioner\Transaction\Presenter\ConsoleCommissionsPresenter;
use Commissioner\Transaction\Repository\PlainFileTransactionsRepository;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpClient\HttpClient;

require_once dirname(__DIR__).'/public/bootstrap/bootstrap.php';

$input = new Input($argv);

$http = HttpClient::create();
$cache = new ArrayAdapter();

$transactionRepository = new PlainFileTransactionsRepository($input->getInputFilePath());

$binNumberCountryResolver = new CacheableBinNumberCountryResolver(
    new ApiBinNumberCountryResolver($http, getenv('BIN_NUMBER_API_URL')), $cache, (int) getenv('BIN_NUMBER_API_CACHE_TTL')
);

$exchangeRateProvider  = new CacheableExchangeRateProvider(
    new ApiExchangeRateProvider($http, getenv('CURRENCY_EXCHANGE_API_URL'), getenv('CURRENCY_EXCHANGE_API_KEY')), $cache, (int) getenv('CURRENCY_EXCHANGE_CACHE_TTL')
);

$euCountryCodes        = explode(',', getenv('EU_COUNTRIES'));
$euCommissionRate      = (float)getenv('COMMISSION_EU');
$defaultCommissionRate = (float)getenv('COMMISSION_DEFAULT');

$commissions = new CountrySpecificCommission(
    $euCountryCodes,
    $euCommissionRate,
    new DefaultCommission($defaultCommissionRate)
);

$commissioner = new Commissioner(
    $transactionRepository,
    $binNumberCountryResolver,
    $exchangeRateProvider,
    $commissions
);

try
{
    $commissions = $commissioner->charge();

    (new ConsoleCommissionsPresenter())->present($commissions);
}
catch (\Exception $e)
{
    echo "An error occured: \n";
    echo "\t{$e->getMessage()}\n";
    echo "\t{$e->getTraceAsString()}\n";
}


