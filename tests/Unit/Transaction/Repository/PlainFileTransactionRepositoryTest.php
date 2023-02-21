<?php declare(strict_types=1);

namespace Commissioner\Tests\Unit\Transaction\Repository;

use Commissioner\Transaction\Repository\InvalidTransactionsDataException;
use Commissioner\Transaction\Repository\PlainFileTransactionsRepository;
use PHPUnit\Framework\TestCase;


/**
 * Class PlainFileTransactionRepositoryTest
 *
 * @package Commissioner\Tests\Unit\Transaction\Repository
 */
class PlainFileTransactionRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function Should_ProvideValidTransactions_When_TransactionsInputHasValidData()
    {
        $inputWithTransactions = <<<JSON
{"bin":"45717360","amount":"100.00","currency":"EUR"}
{"bin":"516793","amount":"50.00","currency":"USD"}
{"bin":"45417360","amount":"10000.00","currency":"JPY"}
{"bin":"41417360","amount":"130.00","currency":"USD"}
{"bin":"4745030","amount":"2000.00","currency":"GBP"}
JSON;

        $path = $this->prePersistContent($inputWithTransactions);

        $transactionsRepository = new PlainFileTransactionsRepository($path);
        $transactions = $transactionsRepository->getTransactionsToProcess();

        unlink($path);

        $this->assertCount(5, $transactions);
    }

    /**
     * @test
     */
    public function Should_ProvideZeroTransactions_When_TransactionsInputIsEmpty()
    {
        $inputWithoutTransactions = <<<JSON
JSON;

        $path = $this->prePersistContent($inputWithoutTransactions);

        $transactionsRepository = new PlainFileTransactionsRepository($path);
        $transactions = $transactionsRepository->getTransactionsToProcess();

        unlink($path);

        $this->assertCount(0, $transactions);
    }

    /**
     * @test
     * @dataProvider getInvalidTransactionDataProvider
     */
    public function Should_ThrowAnException_When_TransactionsInputHasInvalidData($inputContent)
    {
        $this->expectException(InvalidTransactionsDataException::class);
        $this->expectExceptionMessage("Required data parts are missing. Please, check the input.");

        $path = $this->prePersistContent($inputContent);

        $transactionsRepository = new PlainFileTransactionsRepository($path);
        $transactionsRepository->getTransactionsToProcess();

        unlink($path);
    }

    private function prePersistContent($content): string
    {
        @mkdir($tmpdir = sys_get_temp_dir().'/commissioner');

        $path = tempnam($tmpdir, 'cm-');

        file_put_contents($path, $content);

        return $path;
    }

    public function getInvalidTransactionDataProvider(): array
    {
        $contents = [];

        $contents[] = <<<JSON
{"b":"45717360","amount":"100.00","currency":"EUR"}
JSON;

        $contents[] = <<<JSON
{"bin":"45717360","aount":"100.00","currency":"EUR"}
JSON;
        $contents[] = <<<JSON
{"bin":"45717360","amount":"100.00","currsency":"EUR"}
JSON;

        return [
            [$contents[0]],
            [$contents[1]],
            [$contents[2]]
        ];
    }
}
