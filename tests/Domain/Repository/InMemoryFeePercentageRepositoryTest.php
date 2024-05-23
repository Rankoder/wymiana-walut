<?php
declare(strict_types=1);

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
    /**
     * @var InMemoryFeePercentageRepository The repository being tested
     */
    private InMemoryFeePercentageRepository $feePercentageRepository;

    /**
     * Sets up the test environment.
     */
    protected function setUp(): void
    {
        $this->feePercentageRepository = new InMemoryFeePercentageRepository();
    }

    /**
     * Tests the getBuyerFeePercentage method.
     */
    public function testGetBuyerFeePercentage(): void
    {
        $buyerFee = $this->feePercentageRepository->getBuyerFeePercentage();
        $this->assertEquals(99, $buyerFee, "Buyer fee percentage should be 99.");
    }

    /**
     * Tests the getSellerFeePercentage method.
     */
    public function testGetSellerFeePercentage(): void
    {
        $sellerFee = $this->feePercentageRepository->getSellerFeePercentage();
        $this->assertEquals(1, $sellerFee, "Seller fee percentage should be 1.");
    }
}
