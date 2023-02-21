<?php declare(strict_types=1);

namespace Commissioner\Tests\Unit\BinNumber;

use Commissioner\BinNumber\BinNumber;
use PHPUnit\Framework\TestCase;

/**
 * Class BinNumberTest
 *
 * @package Commissioner\Tests\Unit\BinNumber
 */
class BinNumberTest extends TestCase
{
    /**
     * @test
     * @dataProvider getInvalidBinNumbersProvider
     */
    public function Should_ThrowAnException_When_InvalidNumberProvided(string $number)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid bin number. Should be from 4 to 8 numbers long.");

        new BinNumber($number);
    }

    /**
     * @test
     * @dataProvider getValidBinNumbersProvider
     */
    public function Should_CreateBinNumber_When_CorrectDataProvided(string $number)
    {
        $this->assertEquals($number, (new BinNumber($number))->value());
    }

    public function getInvalidBinNumbersProvider(): array
    {
        return [
            [''],
            ['1'],
            ['11'],
            ['111'],
            ['111111111']
        ];
    }

    public function getValidBinNumbersProvider(): array
    {
        return [
            ['45717360'],
            ['516793'],
            ['45417360'],
            ['41417360'],
            ['4745030']
        ];
    }
}
