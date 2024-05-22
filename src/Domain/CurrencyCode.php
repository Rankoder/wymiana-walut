<?php
declare(strict_types=1);

namespace src\Domain;

/**
 * Class for currency code
 */
class CurrencyCode
{
    /** @var string */
    private string $Code;

    /**
     * @param string $Code
     * @throws \Exception
     */
    public function __construct(string $Code)
    {
        if (!in_array($Code, ['EUR', 'GBP'])) {
            throw new \Exception('Wrong currency code');
        }

        $this->Code = $Code;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->Code;
    }
}
