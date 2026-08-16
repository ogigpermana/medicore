<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use App\Models\Product;
use App\Models\Category;
use App\Models\Batch;

/**
 * Product & Inventory Unit Tests
 */
class ProductTest extends TestCase
{
    private Application $app;
    private Product $productModel;
    private Batch $batchModel;
    private Category $categoryModel;

    protected function setUp(): void
    {
        $this->app = Application::create();
        $this->productModel = new Product();
        $this->batchModel = new Batch();
        $this->categoryModel = new Category();
    }

    /**
     * Test product retrieval by SKU
     */
    public function test_find_by_sku(): void
    {
        $product = $this->productModel->findBySku('MED-AMX-500');

        $this->assertNotNull($product, 'Amoxicillin product should be found by SKU');
        $this->assertEquals('Amoxicillin 500mg', $product['name']);
        $this->assertGreaterThan(0, $product['sell_price']);
    }

    /**
     * Test product retrieval by barcode
     */
    public function test_find_by_barcode(): void
    {
        $product = $this->productModel->findByBarcode('8991001234511');

        $this->assertNotNull($product, 'Product should be found by barcode');
        $this->assertEquals('MED-AMX-500', $product['sku']);
    }

    /**
     * Test catalog search filter
     */
    public function test_catalog_search(): void
    {
        $results = $this->productModel->getCatalog(['search' => 'Paracetamol']);

        $this->assertIsArray($results);
        $this->assertNotEmpty($results);
        $this->assertStringContainsString('Paracetamol', $results[0]['name']);
    }

    /**
     * Test FEFO batch priority ordering (Earliest Expiry First)
     */
    public function test_fefo_batch_ordering(): void
    {
        $amox = $this->productModel->findBySku('MED-AMX-500');
        $this->assertNotNull($amox);

        $batches = $this->batchModel->getFefoBatches($amox['id']);
        $this->assertIsArray($batches);

        if (count($batches) >= 2) {
            // First batch should have earlier or equal expiry date than second batch
            $firstExpiry = strtotime($batches[0]['expiry_date']);
            $secondExpiry = strtotime($batches[1]['expiry_date']);
            $this->assertLessThanOrEqual($secondExpiry, $firstExpiry, 'FEFO must prioritize earliest expiring batch first');
        }
    }

    /**
     * Test low stock detection
     */
    public function test_low_stock_detection(): void
    {
        $lowStock = $this->productModel->getLowStock();
        $this->assertIsArray($lowStock);
    }
}
