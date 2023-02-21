<?php declare(strict_types=1);

namespace Commissioner\Transaction;

/**
 * Class TransactionCommission
 *
 * @package Commissioner\Transaction
 */
class TransactionCommission
{
    public function __construct(
        private readonly float $value,
        private readonly Currency $currency
    ) {}

    public function value(): float
    {
        return $this->value;
    }

    public function ceil(int $precision): float
    {
        if ($precision < 0)
        {
            throw new \InvalidArgumentException('Precision should be positive.');
        }

        $pow = 10 ** $precision;

        return ( ceil ( $pow * $this->value ) + ceil ( $pow * $this->value - ceil ( $pow * $this->value ) ) ) / $pow;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }
}
