<?php declare(strict_types=1);

namespace Commissioner;

use Commissioner\BinNumber\BinNumberCountryResolver;
use Commissioner\Commission\Commission;
use Commissioner\Exchange\ExchangeRateProvider;
use Commissioner\Transaction\Currency;
use Commissioner\Transaction\Repository\TransactionsRepository;
use Commissioner\Transaction\TransactionCommission;
use Commissioner\Transaction\TransactionCommissionCollection;


/**
 * Class Commissioner
 *
 * @package Commissioner
 */
class Commissioner
{
    public function __construct(
        private readonly TransactionsRepository $transactions,
        private readonly BinNumberCountryResolver $bin,
        private readonly ExchangeRateProvider $exchange,
        private readonly Commission $commissions,
        private readonly Currency $baseCurrency = new Currency('EUR')
    ) {}

    public function charge(): TransactionCommissionCollection
    {
        $transactions = $this->transactions->getTransactionsToProcess();
        $commissions  = new TransactionCommissionCollection();

        foreach ($transactions as $transaction)
        {
            $countryCode = $this->bin->resolveCountry($transaction->bin());

            $amount     = $transaction->amount();
            $commission = $this->commissions->forCountry($countryCode);

            if (!$transaction->ofCurrency($this->baseCurrency))
            {
                $exchangeRate = $this->exchange->getRate($this->baseCurrency, $transaction->currency());

                if ($exchangeRate > 0)
                {
                    $amount = $transaction->amount() / $exchangeRate;
                }
            }

            $commissions[] = new TransactionCommission($amount * $commission, $this->baseCurrency);
        }

        return $commissions;
    }
}
