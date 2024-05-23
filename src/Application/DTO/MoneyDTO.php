<?php
declare(strict_types=1);

namespace src\Application\DTO;

/**
 * Class MoneyDTO
 *
 * Data Transfer Object for money values.
 */
class MoneyDTO
{
    private string $amount;
    private string $code;

    public function __construct(string $amount, string $code)
    {
        $this->amount = $amount;
        $this->code = $code;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
