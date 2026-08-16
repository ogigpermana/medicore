<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use App\Models\Sale;
use App\Models\CashierShift;
use App\Models\Product;
use App\Models\Batch;
use App\Models\User;

/**
 * Point of Sale (POS) & Billing Unit Tests
 */
class PosTest extends TestCase
{
    private Application $app;
    private Sale $saleModel;
    private CashierShift $shiftModel;
    private Product $productModel;
    private Batch $batchModel;
    private User $userModel;

    protected function setUp(): void
    {
        $this->app = Application::create();
        $this->saleModel = new Sale();
        $this->shiftModel = new CashierShift();
        $this->productModel = new Product();
        $this->batchModel = new Batch();
        $this->userModel = new User();
    }

    /**
     * Test cashier shift lifecycle (open -> active -> close)
     */
    public function test_cashier_shift_lifecycle(): void
    {
        $cashier = $this->userModel->findByEmailWithRole('cashier@medicore.com');
        $this->assertNotNull($cashier);

        // 1. Open shift
        $shiftId = $this->shiftModel->openShift($cashier['id'], 250000.00, 'Shift Test Open');
        $this->assertGreaterThan(0, $shiftId);

        $activeShift = $this->shiftModel->getActiveShift($cashier['id']);
        $this->assertNotNull($activeShift);
        $this->assertEquals('open', $activeShift['status']);
        $this->assertEquals(250000.00, (float)$activeShift['opening_cash']);

        // 2. Close shift
        $closed = $this->shiftModel->closeShift($shiftId, 250000.00, 'Shift Test Close');
        $this->assertTrue($closed);

        $recheckedShift = $this->shiftModel->find($shiftId);
        $this->assertEquals('closed', $recheckedShift['status']);
    }

    /**
     * Test POS checkout with automated FEFO batch allocation
     */
    public function test_pos_checkout_with_fefo_allocation(): void
    {
        $cashier = $this->userModel->findByEmailWithRole('cashier@medicore.com');
        $this->assertNotNull($cashier);

        // Get Amoxicillin which has FEFO batches seeded
        $amox = $this->productModel->findBySku('MED-AMX-500');
        $this->assertNotNull($amox);

        $initialStock = (int)$amox['stock_quantity'];
        $this->assertGreaterThan(5, $initialStock);

        $batchesBefore = $this->batchModel->getFefoBatches($amox['id']);
        $this->assertNotEmpty($batchesBefore);
        $firstBatchBeforeQty = (int)$batchesBefore[0]['current_quantity'];
        
        if ($firstBatchBeforeQty < 5) {
            $this->batchModel->update((int)$batchesBefore[0]['id'], ['current_quantity' => 50]);
            $firstBatchBeforeQty = 50;
            $this->productModel->update((int)$amox['id'], ['stock_quantity' => 100]);
            $initialStock = 100;
        }

        // Perform checkout for 2 units
        $salePayload = [
            'user_id' => $cashier['id'],
            'customer_name' => 'John Doe (Test Patient)',
            'customer_phone' => '081299998888',
            'discount_amount' => 0.00,
            'include_tax' => true,
            'payment_method' => 'cash',
            'cash_tendered' => 50000.00,
            'notes' => 'Unit test transaction'
        ];

        $cartItems = [
            [
                'product_id' => $amox['id'],
                'quantity' => 2,
                'unit_price' => (float)$amox['sell_price']
            ]
        ];

        $result = $this->saleModel->processCheckout($salePayload, $cartItems);

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['invoice_number']);
        $this->assertEquals(2 * (float)$amox['sell_price'], $result['subtotal']);
        $this->assertGreaterThan(0, $result['tax_amount'], 'Tax should be calculated when include_tax is true');
        $this->assertGreaterThan(0, $result['total_amount']);

        // Verify product stock was decremented
        $amoxAfter = $this->productModel->find($amox['id']);
        $this->assertEquals($initialStock - 2, (int)$amoxAfter['stock_quantity']);

        // Verify earliest FEFO batch was decremented
        $batchesAfter = $this->batchModel->getFefoBatches($amox['id']);
        $firstBatchAfterQty = (int)$batchesAfter[0]['current_quantity'];
        $this->assertEquals($firstBatchBeforeQty - 2, $firstBatchAfterQty, 'FEFO must deduct from earliest batch');
    }

    /**
     * Test invoice details retrieval for receipt printing
     */
    public function test_get_sale_with_details(): void
    {
        $sales = $this->saleModel->getRecentSales(1);
        $this->assertNotEmpty($sales);

        $saleId = $sales[0]['id'];
        $detailedSale = $this->saleModel->getWithDetails($saleId);

        $this->assertNotNull($detailedSale);
        $this->assertArrayHasKey('items', $detailedSale);
        $this->assertNotEmpty($detailedSale['items']);
        $this->assertArrayHasKey('product_name', $detailedSale['items'][0]);
    }
}
