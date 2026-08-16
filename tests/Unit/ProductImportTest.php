<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use Core\Request;
use App\Models\Product;
use App\Models\Batch;
use App\Controllers\ProductController;

/**
 * Product CSV Import Unit Tests
 */
class ProductImportTest extends TestCase
{
    private Application $app;
    private Product $productModel;
    private Batch $batchModel;
    private ProductController $controller;

    protected function setUp(): void
    {
        $this->app = Application::create();
        $this->productModel = new Product();
        $this->batchModel = new Batch();
        
        $request = new Request();
        $this->controller = new ProductController($request, $this->app->getContainer());

        // Login as superadmin
        $session = $this->app->getContainer()->get(\Core\Session::class);
        $auth = new \Core\Auth($session);
        $userModel = new \App\Models\User();
        $admin = $userModel->findByEmailWithRole('admin@medicore.com');
        $auth->login($admin);
    }

    /**
     * Test CSV template generation
     */
    public function test_download_template(): void
    {
        $request = new Request();
        $response = $this->controller->downloadTemplate($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', $response->getHeader('Content-Type'));
        $this->assertStringContainsString('SKU,Barcode,Name', $response->getContent());
    }

    /**
     * Test bulk CSV import parsing and creation
     */
    public function test_import_medications_csv(): void
    {
        $testSku = 'MED-IMP-' . strtoupper(substr(uniqid(), -4));
        $testBarcode = '899' . mt_rand(1000000000, 9999999999);
        $csvData = "SKU,Barcode,Name,GenericName,CategorySlug,UnitSymbol,Dosage,Manufacturer,BuyPrice,SellPrice,MinStock,StockQuantity,RequiresPrescription,BatchNumber,ExpiryDate\n" .
                   "{$testSku},{$testBarcode},Ibuprofen 400mg Forte,Ibuprofen,analgesics,str,400mg,Kalbe Farma,12000,16500,15,60,0,LOT-IBU-2026,2027-10-31";

        $request = new Request();
        $request->setMethod('POST');
        $request->setUri('/inventory/products/import');
        // Populate request data
        $ref = new \ReflectionClass($request);
        $prop = $ref->getProperty('data');
        $prop->setAccessible(true);
        $prop->setValue($request, ['csv_data' => $csvData]);

        $response = $this->controller->importCsv($request);

        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);

        $this->assertTrue($body['success']);
        $this->assertGreaterThanOrEqual(1, $body['imported']);

        // Verify product was created in database
        $importedProduct = $this->productModel->findBySku($testSku);
        $this->assertNotNull($importedProduct);
        $this->assertEquals('Ibuprofen 400mg Forte', $importedProduct['name']);
        $this->assertEquals(16500.00, (float)$importedProduct['sell_price']);
        $this->assertEquals(60, (int)$importedProduct['stock_quantity']);

        // Verify FEFO batch lot was also created
        $batches = $this->batchModel->getFefoBatches($importedProduct['id']);
        $this->assertNotEmpty($batches);
        $this->assertEquals('LOT-IBU-2026', $batches[0]['batch_number']);
        $this->assertEquals(60, (int)$batches[0]['current_quantity']);
    }
}
