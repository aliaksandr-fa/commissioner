<?php declare(strict_types=1);

namespace Commissioner\CLI;

use InvalidArgumentException;

/**
 * Class Input
 *
 */
class Input
{
    public function __construct(private readonly array $argv) {}

    public function getInputFilePath()
    {
        return $this->argv[1] ?? new InvalidArgumentException("Please, provide path to the input file.");
    }
}