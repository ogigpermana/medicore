<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use Core\Database;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\Supplier;

class PurchasingTest extends TestCase
{
    private static $container;
    private PurchaseOrder $poModel;
    private GoodsReceipt $grModel;
    private Product $productModel;
    private Supplier $supplierModel;
    private $db;

    public static function setUpBeforeClass(): void
    {
        $app = Application::create();
        self::$container = $app->getContainer();
    }

    protected function setUp(): void
    {
        $this->db = self::$container->get(Database::class);
        $this->poModel = new PurchaseOrder($this->db);
        $this->grModel = new GoodsReceipt($this->db);
        $this->productModel = new Product($this->db);
        $this->supplierModel = new Supplier($this->db);
    }

    public function testGeneratePoNumberForDifferentSpTypes(): void
    {
        $regPo = $this->poModel->generatePoNumber('regular');
        $this->assertStringStartsWith('SP-REG-' . date('Ymd'), $regPo);

        $prkPo = $this->poModel->generatePoNumber('precursor');
        $this->assertStringStartsWith('SP-PRK-' . date('Ymd'), $prkPo);

        $ootPo = $this->poModel->generatePoNumber('oot');
        $this->assertStringStartsWith('SP-OOT-' . date('Ymd'), $ootPo);

        $nktPo = $this->poModel->generatePoNumber('narcotic_psychotropic');
        $this->assertStringStartsWith('SP-NKT-' . date('Ymd'), $nktPo);
    }

    public function testCreatePurchaseOrder(): void
    {
        $suppliers = $this->supplierModel->all();
        $this->assertNotEmpty($suppliers, "Should have suppliers in database");
        $products = $this->productModel->all();
        $this->assertNotEmpty($products, "Should have products in database");

        $supplier = $suppliers[0];
        $prod1 = $products[0];
        $prod2 = $products[1] ?? $products[0];

        $poNumber = $this->poModel->generatePoNumber('regular');

        $poData = [
            'po_number' => $poNumber,
            'sp_type' => 'regular',
            'supplier_id' => $supplier['id'],
            'user_id' => 1,
            'order_date' => date('Y-m-d'),
            'expected_delivery_date' => date('Y-m-d', strtotime('+3 days')),
            'status' => 'ordered',
            'payment_terms' => 'net_30',
            'subtotal' => 500000.00,
            'discount_amount' => 0.00,
            'tax_amount' => 55000.00,
            'grand_total' => 555000.00,
            'notes' => 'Unit testing BPOM Surat Pesanan Reguler',
            'pharmacist_sipa' => 'SIPA: 19880415/SIPA_32.73/2022/2001'
        ];

        $items = [
            [
                'product_id' => $prod1['id'],
                'quantity' => 20,
                'unit_price' => (float)$prod1['buy_price'],
                'discount_percent' => 0.0,
                'tax_percent' => 11.0
            ],
            [
                'product_id' => $prod2['id'],
                'quantity' => 15,
                'unit_price' => (float)$prod2['buy_price'],
                'discount_percent' => 0.0,
                'tax_percent' => 11.0
            ]
        ];

        $poId = $this->poModel->createPurchaseOrder($poData, $items);
        $this->assertGreaterThan(0, $poId);

        $fetched = $this->poModel->getDetails($poId);
        $this->assertNotNull($fetched);
        $this->assertEquals($poNumber, $fetched['po_number']);
        $this->assertEquals('regular', $fetched['sp_type']);
        $this->assertCount(2, $fetched['items']);
    }

    public function testReceiveGoodsAndAutoReplenishBatches(): void
    {
        $suppliers = $this->supplierModel->all();
        $products = $this->productModel->all();

        $supplier = $suppliers[0];
        $prod = $products[0];

        // 1. Create a PO first
        $poNumber = $this->poModel->generatePoNumber('precursor');
        $poId = $this->poModel->createPurchaseOrder([
            'po_number' => $poNumber,
            'sp_type' => 'precursor',
            'supplier_id' => $supplier['id'],
            'user_id' => 1,
            'order_date' => date('Y-m-d'),
            'status' => 'ordered',
            'payment_terms' => 'net_14',
            'subtotal' => 300000.00,
            'grand_total' => 333000.00
        ], [
            [
                'product_id' => $prod['id'],
                'quantity' => 10,
                'unit_price' => 30000.00
            ]
        ]);

        $initialProduct = $this->productModel->find($prod['id']);
        $initialStock = (int)$initialProduct['stock_quantity'];

        // 2. Receive Goods against this PO
        $grnNumber = $this->grModel->generateGrnNumber();
        $batchNo = 'TEST-BATCH-' . date('YmdHis');

        $grId = $this->grModel->receiveGoods([
            'grn_number' => $grnNumber,
            'purchase_order_id' => $poId,
            'supplier_id' => $supplier['id'],
            'received_by' => 1,
            'invoice_number' => 'INV-TEST-' . rand(1000, 9999),
            'invoice_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+14 days')),
            'subtotal' => 300000.00,
            'tax_amount' => 33000.00,
            'total_amount' => 333000.00,
            'notes' => 'Testing GRN Goods Receipt auto-stock'
        ], [
            [
                'product_id' => $prod['id'],
                'batch_number' => $batchNo,
                'expiry_date' => date('Y-m-d', strtotime('+18 months')),
                'quantity_received' => 10,
                'buy_price' => 30000.00
            ]
        ]);

        $this->assertGreaterThan(0, $grId);

        // Verify total stock increased by 10
        $updatedProduct = $this->productModel->find($prod['id']);
        $this->assertEquals($initialStock + 10, (int)$updatedProduct['stock_quantity']);

        // Verify PO status automatically transitioned to 'received'
        $updatedPo = $this->poModel->getDetails($poId);
        $this->assertEquals('received', $updatedPo['status']);
        $this->assertEquals(10, $updatedPo['items'][0]['quantity_received']);

        // Verify Goods Receipt Details
        $grDetails = $this->grModel->getDetails($grId);
        $this->assertNotNull($grDetails);
        $this->assertEquals($grnNumber, $grDetails['grn_number']);
        $this->assertEquals('unpaid', $grDetails['payment_status']);
    }

    public function testRecordAccountsPayablePayment(): void
    {
        $suppliers = $this->supplierModel->all();
        $products = $this->productModel->all();

        // Create standalone goods receipt note
        $grnNumber = $this->grModel->generateGrnNumber();
        $grId = $this->grModel->receiveGoods([
            'grn_number' => $grnNumber,
            'purchase_order_id' => null,
            'supplier_id' => $suppliers[0]['id'],
            'received_by' => 1,
            'invoice_number' => 'INV-AP-' . rand(10000, 99999),
            'invoice_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'subtotal' => 1000000.00,
            'tax_amount' => 110000.00,
            'total_amount' => 1110000.00
        ], [
            [
                'product_id' => $products[0]['id'],
                'batch_number' => 'AP-BATCH-' . rand(100, 999),
                'expiry_date' => date('Y-m-d', strtotime('+24 months')),
                'quantity_received' => 5,
                'buy_price' => 200000.00
            ]
        ]);

        // 1. Partial Payment of Rp 500.000
        $pay1 = $this->grModel->recordPayment($grId, 500000.00, 'bank_transfer', 'REF-TRF-001', 'Partial DP 500rb', 1);
        $this->assertTrue($pay1);

        $gr1 = $this->grModel->getDetails($grId);
        $this->assertEquals('partial', $gr1['payment_status']);
        $this->assertEquals(500000.00, (float)$gr1['amount_paid']);
        $this->assertCount(1, $gr1['payments']);

        // 2. Full Settlement Payment of remaining Rp 610.000
        $pay2 = $this->grModel->recordPayment($grId, 610000.00, 'bank_transfer', 'REF-TRF-002', 'Final settlement', 1);
        $this->assertTrue($pay2);

        $gr2 = $this->grModel->getDetails($grId);
        $this->assertEquals('paid', $gr2['payment_status']);
        $this->assertEquals(1110000.00, (float)$gr2['amount_paid']);
        $this->assertCount(2, $gr2['payments']);
    }

    public function testApSummaryMetrics(): void
    {
        $summary = $this->grModel->getApSummary();
        $this->assertArrayHasKey('total_invoiced', $summary);
        $this->assertArrayHasKey('total_paid', $summary);
        $this->assertArrayHasKey('total_outstanding', $summary);
        $this->assertGreaterThanOrEqual(0, (float)$summary['total_outstanding']);
    }
}
