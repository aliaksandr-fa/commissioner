<?php declare(strict_types=1);

namespace Commissioner\Transaction;


/**
 * Class Currency
 *
 * @package Commissioner\Transaction
 */
class Currency
{
    public function __construct(private string $code)
    {
        if (preg_match('/^[a-zA-Z]{3}$/', $this->code) !== 1)
        {
            throw new \InvalidArgumentException('Invalid currency code.');
        }

        $this->code = strtoupper($code);
    }

    public function code(): string
    {
        return $this->code;
    }
}
