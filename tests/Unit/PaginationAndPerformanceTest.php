<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use Core\Database;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;

class PaginationAndPerformanceTest extends TestCase
{
    private static $container;
    private $db;
    private Product $productModel;
    private Sale $saleModel;
    private Customer $customerModel;

    public static function setUpBeforeClass(): void
    {
        $app = Application::create();
        self::$container = $app->getContainer();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = self::$container->get(Database::class);
        $this->productModel = new Product($this->db);
        $this->saleModel = new Sale($this->db);
        $this->customerModel = new Customer($this->db);
    }

    public function testMedicationCatalogServerSidePagination(): void
    {
        // 1. Fetch paginated catalog with 10 per page
        $res10 = $this->productModel->getCatalogPaginated([], 1, 10);
        $this->assertArrayHasKey('items', $res10);
        $this->assertArrayHasKey('total', $res10);
        $this->assertArrayHasKey('page', $res10);
        $this->assertArrayHasKey('per_page', $res10);
        $this->assertArrayHasKey('total_pages', $res10);
        $this->assertArrayHasKey('from', $res10);
        $this->assertArrayHasKey('to', $res10);

        $this->assertEquals(1, $res10['page']);
        $this->assertEquals(10, $res10['per_page']);
        $this->assertLessThanOrEqual(10, count($res10['items']));

        // 2. Fetch with search query
        $resSearch = $this->productModel->getCatalogPaginated(['search' => 'Paracetamol'], 1, 25);
        $this->assertIsArray($resSearch['items']);

        // 3. Offset calculation
        $resPage2 = $this->productModel->getCatalogPaginated([], 2, 5);
        $this->assertEquals(2, $resPage2['page']);
        $this->assertEquals(5, $resPage2['per_page']);
        $this->assertEquals(6, $resPage2['from']);
    }

    public function testSalesHistoryServerSidePagination(): void
    {
        $res = $this->saleModel->getSalesPaginated([], 1, 25);
        $this->assertArrayHasKey('items', $res);
        $this->assertArrayHasKey('total', $res);
        $this->assertArrayHasKey('page', $res);
        $this->assertArrayHasKey('total_pages', $res);
    }

    public function testCustomerCrmServerSidePagination(): void
    {
        $res = $this->customerModel->getCustomersPaginated(null, 'all', 'all', 1, 10);
        $this->assertArrayHasKey('items', $res);
        $this->assertArrayHasKey('total', $res);
        $this->assertArrayHasKey('page', $res);
        $this->assertArrayHasKey('total_pages', $res);
        $this->assertLessThanOrEqual(10, count($res['items']));
    }
}
