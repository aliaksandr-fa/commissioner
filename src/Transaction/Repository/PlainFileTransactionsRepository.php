<?php declare(strict_types=1);

namespace Commissioner\Transaction\Repository;

use Commissioner\BinNumber\BinNumber;
use Commissioner\Transaction\Currency;
use Commissioner\Transaction\Transaction;
use Commissioner\Transaction\TransactionCollection;


/**
 * Class PlainFileTransactionsRepository
 *
 * @package Commissioner\Transaction
 */
class PlainFileTransactionsRepository implements TransactionsRepository
{
    public function __construct(
        private string $pathToInputFile
    ) {}

    /**
     * @throws InvalidTransactionsDataException
     *
     * @return TransactionCollection
     */
    public function getTransactionsToProcess(): TransactionCollection
    {
        $raw = $this->getTransactionsRows();
        $transactions = new TransactionCollection();

        foreach ($raw as $transactionData)
        {
            $this->validateTransactionData($transactionData);

            $transactions[] = new Transaction(
                new BinNumber($transactionData['bin']),
                floatval($transactionData['amount']),
                new Currency($transactionData['currency']),
            );
        }

        return $transactions;
    }

    private function getTransactionsRows(): array
    {
        $rows = explode("\n", file_get_contents($this->pathToInputFile));

        $rows = array_filter($rows);

        return array_map(fn ($json) => json_decode($json, true), $rows);
    }

    private function validateTransactionData(array $data): void
    {
        if (!array_key_exists('bin', $data)
            || !array_key_exists('amount', $data)
            || !array_key_exists('currency', $data))
        {
            throw new InvalidTransactionsDataException("Required data parts are missing. Please, check the input.");
        }
    }
}
