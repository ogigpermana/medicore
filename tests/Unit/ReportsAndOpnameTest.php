<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Report;
use App\Models\StockOpname;
use App\Models\Product;
use Core\Database;

class ReportsAndOpnameTest extends TestCase
{
    private static $container;
    private $db;
    private Report $reportModel;
    private StockOpname $opnameModel;

    public static function setUpBeforeClass(): void
    {
        $app = \Core\Application::create();
        self::$container = $app->getContainer();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = self::$container->get(Database::class);
        $this->reportModel = new Report($this->db);
        $this->opnameModel = new StockOpname($this->db);
    }

    public function testProfitLossCalculation(): void
    {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-d');

        $pl = $this->reportModel->getProfitLoss($startDate, $endDate);

        $this->assertIsArray($pl);
        $this->assertArrayHasKey('net_sales', $pl);
        $this->assertArrayHasKey('total_cogs', $pl);
        $this->assertArrayHasKey('gross_profit', $pl);
        $this->assertArrayHasKey('profit_margin', $pl);
        $this->assertEquals($pl['net_sales'] - $pl['total_cogs'], $pl['gross_profit']);
    }

    public function testInventoryValuation(): void
    {
        $val = $this->reportModel->getInventoryValuation();

        $this->assertIsArray($val);
        $this->assertArrayHasKey('summary', $val);
        $this->assertArrayHasKey('category_breakdown', $val);
        $this->assertArrayHasKey('products', $val);
        $this->assertGreaterThanOrEqual(0, $val['summary']['total_asset_buy_value']);
    }

    public function testStockOpnameFullLifecycle(): void
    {
        $user = $this->db->fetch("SELECT u.id FROM users u JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id WHERE r.name IN ('superadmin', 'pharmacist') LIMIT 1");
        $userId = $user ? (int)$user['id'] : 1;

        // 1. Start Opname
        $opnameId = $this->opnameModel->startOpname('Unit Test Stock Opname', $userId, null, 'Automated Test Opname');
        $this->assertGreaterThan(0, $opnameId);

        $so = $this->opnameModel->getDetails($opnameId);
        $this->assertNotNull($so);
        $this->assertEquals('in_progress', $so['status']);
        $this->assertNotEmpty($so['items']);

        // 2. Save physical count with variance
        $firstItem = $so['items'][0];
        $sysQty = (int)$firstItem['system_qty'];
        $newPhysQty = $sysQty + 5; // Variance +5

        $itemsToSave = [
            [
                'id' => (int)$firstItem['id'],
                'system_qty' => $sysQty,
                'physical_qty' => $newPhysQty,
                'buy_price' => (float)$firstItem['buy_price'],
                'adjustment_reason' => 'bonus_sample',
                'notes' => 'Bonus test sample'
            ]
        ];

        $saved = $this->opnameModel->saveCounts($opnameId, $itemsToSave);
        $this->assertTrue($saved);

        // 3. Approve and Reconcile
        $approved = $this->opnameModel->approveAndReconcile($opnameId, $userId);
        $this->assertTrue($approved);

        $soCompleted = $this->opnameModel->getDetails($opnameId);
        $this->assertEquals('completed', $soCompleted['status']);
        $this->assertEquals($userId, $soCompleted['approved_by']);

        // Verify product stock was updated
        $updatedProd = $this->db->fetch("SELECT stock_quantity FROM products WHERE id = ?", [$firstItem['product_id']]);
        $this->assertEquals($newPhysQty, (int)$updatedProd['stock_quantity']);
    }
}
