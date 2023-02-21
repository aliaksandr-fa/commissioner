<?php declare(strict_types=1);

namespace Commissioner\Transaction;
use ArrayIterator;

/**
 * Class TransactionCommissionCollection
 *
 * @package Commissioner\Transaction
 */
class TransactionCommissionCollection extends ArrayIterator
{
    public function __construct(TransactionCommission ...$commissions)
    {
        parent::__construct($commissions);
    }

    public function current() : TransactionCommission
    {
        return parent::current();
    }

    public function offsetGet($offset) : TransactionCommission
    {
        return parent::offsetGet($offset);
    }
}