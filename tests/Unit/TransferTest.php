<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use Core\Database;
use App\Models\StockTransfer;
use App\Models\Branch;
use App\Models\Product;

class TransferTest extends TestCase
{
    private static $container;
    private $db;
    private StockTransfer $transferModel;
    private Branch $branchModel;
    private Product $productModel;

    public static function setUpBeforeClass(): void
    {
        $app = Application::create();
        self::$container = $app->getContainer();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = self::$container->get(Database::class);
        $this->transferModel = new StockTransfer($this->db);
        $this->branchModel = new Branch($this->db);
        $this->productModel = new Product($this->db);
    }

    public function testGetActiveBranches(): void
    {
        $branches = $this->branchModel->getActive();
        $this->assertIsArray($branches);
        $this->assertNotEmpty($branches);

        $codes = array_column($branches, 'code');
        $this->assertContains('CB-PST', $codes);
        $this->assertContains('CB-BRT', $codes);
    }

    public function testTransferFullLifecycle(): void
    {
        $branches = $this->branchModel->getActive();
        $sourceBranch = $branches[0];
        $destBranch = $branches[1];

        $user = $this->db->fetch("SELECT u.id FROM users u JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id WHERE r.name IN ('superadmin', 'pharmacist') LIMIT 1");
        $userId = $user ? (int)$user['id'] : 1;

        $product = $this->productModel->all()[0];
        $initialStock = (int)$product['stock_quantity'];

        // Ensure sufficient initial stock
        if ($initialStock < 50) {
            $this->productModel->update((int)$product['id'], ['stock_quantity' => 100]);
            $initialStock = 100;
        }

        // 1. Create Transfer Request
        $transferData = [
            'source_branch_id' => $sourceBranch['id'],
            'destination_branch_id' => $destBranch['id'],
            'requested_by' => $userId,
            'status' => 'pending_approval',
            'shipping_notes' => 'Unit test transfer dispatch'
        ];

        $items = [
            [
                'product_id' => (int)$product['id'],
                'batch_id' => null,
                'qty_requested' => 15,
                'unit_buy_price' => (float)$product['buy_price'],
                'notes' => 'Test line item'
            ]
        ];

        $transferId = $this->transferModel->createTransfer($transferData, $items);
        $this->assertGreaterThan(0, $transferId);

        $trf = $this->transferModel->getDetails($transferId);
        $this->assertNotNull($trf);
        $this->assertEquals('pending_approval', $trf['status']);
        $this->assertEquals(15, (int)$trf['total_qty_sent']);

        // 2. Dispatch Transfer (In-Transit & Stock Deduction)
        $dispatched = $this->transferModel->dispatchTransfer(
            $transferId,
            $userId,
            'Test Courier',
            'B 9999 TEST',
            'Dispatched via refrigerated van'
        );
        $this->assertTrue($dispatched);

        $trfDispatched = $this->transferModel->getDetails($transferId);
        $this->assertEquals('in_transit', $trfDispatched['status']);
        $this->assertEquals('Test Courier', $trfDispatched['driver_name']);

        // Verify stock deduction from source
        $prodAfterDispatch = $this->productModel->find((int)$product['id']);
        $this->assertEquals($initialStock - 15, (int)$prodAfterDispatch['stock_quantity']);

        // 3. Receive Transfer at Destination Branch
        $itemId = (int)$trfDispatched['items'][0]['id'];
        $receivedItems = [
            [
                'id' => $itemId,
                'qty_received' => 15,
                'notes' => 'Received in perfect condition'
            ]
        ];

        $received = $this->transferModel->receiveTransfer($transferId, $userId, $receivedItems);
        $this->assertTrue($received);

        $trfCompleted = $this->transferModel->getDetails($transferId);
        $this->assertEquals('received', $trfCompleted['status']);
        $this->assertEquals(15, (int)$trfCompleted['total_qty_received']);

        // Verify stock credit
        $prodAfterReceive = $this->productModel->find((int)$product['id']);
        $this->assertEquals($initialStock, (int)$prodAfterReceive['stock_quantity']);
    }
}
