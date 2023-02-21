<?php declare(strict_types=1);

namespace Commissioner\Transaction;

use ArrayIterator;


/**
 * Class TransactionCollection
 *
 * @package Commissioner\Transaction
 */
class TransactionCollection extends ArrayIterator
{
    public function __construct(Transaction ...$transactions)
    {
        parent::__construct($transactions);
    }

    public function current() : Transaction
    {
        return parent::current();
    }

    public function offsetGet($offset) : Transaction
    {
        return parent::offsetGet($offset);
    }
}