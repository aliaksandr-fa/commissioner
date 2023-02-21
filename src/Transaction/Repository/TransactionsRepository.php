<?php declare(strict_types=1);

namespace Commissioner\Transaction\Repository;

use Commissioner\Transaction\TransactionCollection;


/**
 * Interface TransactionsRepository
 *
 * @package Commissioner\Transaction\Repository
 */
interface TransactionsRepository
{
    /**
     * @throws InvalidTransactionsDataException
     *
     * @return TransactionCollection
     */
    public function getTransactionsToProcess(): TransactionCollection;
}
