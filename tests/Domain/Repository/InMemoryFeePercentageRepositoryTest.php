<?php

namespace tests\Domain\Repository;

use PHPUnit\Framework\TestCase;
use src\Domain\Repository\InMemoryFeePercentageRepository;

/**
 * Class InMemoryFeePercentageRepositoryTest
 *
 * Unit tests for InMemoryFeePercentageRepository.
 */
class InMemoryFeePercentageRepositoryTest extends TestCase
{
    private InMemoryFeePercentageRepository $feePercentageRepository;

    protected function setUp(): void
    {
        $this->feePercentageRepository = new InMemoryFeePercentageRepository();
    }

    public function testGetBuyerFeePercentage()
    {
        $buyerFee = $this->feePercentageRepository->getBuyerFeePercentage();
        $this->assertEquals(99, $buyerFee, "Buyer fee percentage should be 99.");
    }

    public function testGetSellerFeePercentage()
    {
        $sellerFee = $this->feePercentageRepository->getSellerFeePercentage();
        $this->assertEquals(1, $sellerFee, "Seller fee percentage should be 1.");
    }
}
