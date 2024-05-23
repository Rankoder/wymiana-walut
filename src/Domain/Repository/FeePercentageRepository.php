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
     * @return int The fee percentage for buyers
     */
    public function getBuyerFeePercentage(): int;

    /**
     * Gets the fee percentage for sellers.
     *
     * @return int The fee percentage for sellers
     */
    public function getSellerFeePercentage(): int;
}
