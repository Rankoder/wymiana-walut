<?php
declare(strict_types=1);

namespace src\Domain\Repository;

/**
 * Interface FeePercentageRepository
 *
 * Defines the contract for fee percentage repositories.
 */
interface FeePercentageRepository
{
    /**
     * Gets the fee percentage for buyers.
     *
     * @return int
     */
    public function getBuyerFeePercentage(): int;

    /**
     * Gets the fee percentage for sellers.
     *
     * @return int
     */
    public function getSellerFeePercentage(): int;
}
