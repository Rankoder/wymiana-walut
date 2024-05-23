<?php

namespace src\Domain\Repository;

/**
 * Class InMemoryFeePercentageRepository
 *
 * In-memory implementation of FeePercentageRepository for demonstration purposes.
 */
class InMemoryFeePercentageRepository implements FeePercentageRepository
{
    private array $feePercentages;

    public function __construct()
    {
        $config = require __DIR__ . '/../../Config/config.php';
        $this->feePercentages = $config['fee_percentages'];
    }

    public function getBuyerFeePercentage(): int
    {
        return $this->feePercentages['buyer'];
    }

    public function getSellerFeePercentage(): int
    {
        return $this->feePercentages['seller'];
    }
}
