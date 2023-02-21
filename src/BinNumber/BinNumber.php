<?php declare(strict_types=1);

namespace Commissioner\BinNumber;


/**
 * Class BinNumber
 *
 * @package Commissioner\BinNumber
 */
class BinNumber
{
    public function __construct(private string $value)
    {
        if (preg_match('/^\d{4,8}$/', $this->value) !== 1)
        {
            throw new \InvalidArgumentException('Invalid bin number. Should be from 4 to 8 numbers long.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
