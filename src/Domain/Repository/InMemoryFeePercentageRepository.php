<?php
declare(strict_types=1);

namespace src\Domain\Repository;

/**
 * Class InMemoryFeePercentageRepository
 *
 * In-memory implementation of FeePercentageRepository for demonstration purposes.
 */
class InMemoryFeePercentageRepository implements FeePercentageRepository
{
    /**
     * @var array An associative array of fee percentages
     */
    private array $feePercentages;

    /**
     * InMemoryFeePercentageRepository constructor.
     *
     * Loads fee percentages from configuration.
     */
    public function __construct()
    {
        $config = require __DIR__ . '/../../Config/config.php';
        $this->feePercentages = $config['fee_percentages'];
    }

    /**
     * Gets the fee percentage for buyers.
     *
     * @return int The fee percentage for buyers
     */
    public function getBuyerFeePercentage(): int
    {
        return $this->feePercentages['buyer'];
    }

    /**
     * Gets the fee percentage for sellers.
     *
     * @return int The fee percentage for sellers
     */
    public function getSellerFeePercentage(): int
    {
        return $this->feePercentages['seller'];
    }
}
