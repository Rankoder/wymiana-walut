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
    /**
     * @var string The amount of money
     */
    private string $amount;

    /**
     * @var string The currency code
     */
    private string $code;

    /**
     * MoneyDTO constructor.
     *
     * @param string $amount The amount of money
     * @param string $code The currency code
     */
    public function __construct(string $amount, string $code)
    {
        $this->amount = $amount;
        $this->code = $code;
    }

    /**
     * Gets the amount of money.
     *
     * @return string The amount of money
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * Gets the currency code.
     *
     * @return string The currency code
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
