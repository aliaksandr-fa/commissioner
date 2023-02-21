<?php declare(strict_types=1);

namespace Commissioner\Transaction;


use Commissioner\BinNumber\BinNumber;

/**
 * Class Transaction
 *
 * @package Commissioner\Transaction
 */
class Transaction
{
    public function __construct(
        private readonly BinNumber $bin,
        private readonly float $amount,
        private readonly Currency $currency
    )
    {}

    public function bin(): BinNumber
    {
        return $this->bin;
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function ofCurrency(Currency $currency): bool
    {
        return strtoupper($this->currency->code()) === strtoupper($currency->code());
    }

//    public static function create(): static
//    {
//        return new static;
//    }
}
