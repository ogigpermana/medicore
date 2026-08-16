<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\Supplier;
use App\Models\Product;

class PurchasingController extends Controller
{
    private PurchaseOrder $poModel;
    private GoodsReceipt $grModel;
    private Supplier $supplierModel;
    private Product $productModel;
    private Auth $auth;

    public function __construct(Request $request, $container)
    {
        parent::__construct($request, $container);
        $this->poModel = new PurchaseOrder();
        $this->grModel = new GoodsReceipt();
        $this->supplierModel = new Supplier();
        $this->productModel = new Product();
        $this->auth = $container->get(Auth::class);
    }

    /**
     * Check if user has permission to access Purchasing module
     */
    private function checkAccess(): ?Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $user = $this->auth->user();
        $role = $user['role_name'] ?? 'cashier';

        // Superadmin, Owner, Pharmacist, Warehouse have purchasing access
        if (!in_array($role, ['superadmin', 'owner', 'pharmacist', 'warehouse'])) {
            return $this->redirect('/dashboard');
        }

        return null;
    }

    /**
     * List Purchase Orders (SP Queue)
     */
    public function index(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $user = $this->auth->user();
        $status = $request->get('status', 'all');
        $spType = $request->get('sp_type', 'all');

        $orders = $this->poModel->getList($status, $spType);
        $statusCounts = $this->poModel->getCountsByStatus();

        return $this->render('purchasing/index', [
            'title' => 'Purchase Orders & BPOM Surat Pesanan | MediCore ERP',
            'user' => $user,
            'orders' => $orders,
            'currentStatus' => $status,
            'currentSpType' => $spType,
            'statusCounts' => $statusCounts
        ]);
    }

    /**
     * Create new Purchase Order & BPOM Surat Pesanan Form
     */
    public function create(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $user = $this->auth->user();
        $suppliers = $this->supplierModel->all();
        $products = $this->productModel->all();
        $nextPoNumber = $this->poModel->generatePoNumber('regular');

        return $this->render('purchasing/create', [
            'title' => 'Create Purchase Order (Surat Pesanan) | MediCore ERP',
            'user' => $user,
            'suppliers' => $suppliers,
            'products' => $products,
            'nextPoNumber' => $nextPoNumber
        ]);
    }

    /**
     * Store new Purchase Order
     */
    public function store(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $user = $this->auth->user();
        $data = json_decode($request->getBody(), true) ?: $request->all();

        if (empty($data['supplier_id']) || empty($data['items'])) {
            return $this->json(['success' => false, 'message' => 'Supplier and medication items are required.'], 422);
        }

        try {
            $poData = [
                'po_number' => $this->poModel->generatePoNumber($data['sp_type'] ?? 'regular'),
                'sp_type' => $data['sp_type'] ?? 'regular',
                'supplier_id' => (int)$data['supplier_id'],
                'user_id' => (int)$user['id'],
                'order_date' => $data['order_date'] ?? date('Y-m-d'),
                'expected_delivery_date' => !empty($data['expected_delivery_date']) ? $data['expected_delivery_date'] : null,
                'status' => 'ordered',
                'payment_terms' => $data['payment_terms'] ?? 'net_30',
                'subtotal' => (float)($data['subtotal'] ?? 0),
                'discount_amount' => (float)($data['discount_amount'] ?? 0),
                'tax_amount' => (float)($data['tax_amount'] ?? 0),
                'grand_total' => (float)($data['grand_total'] ?? 0),
                'notes' => $data['notes'] ?? null,
                'pharmacist_sipa' => $data['pharmacist_sipa'] ?? 'SIPA: 19880415/SIPA_32.73/2022/2001'
            ];

            $poId = $this->poModel->createPurchaseOrder($poData, $data['items']);

            return $this->json([
                'success' => true,
                'message' => "Purchase Order {$poData['po_number']} created successfully.",
                'po_id' => $poId,
                'redirect_url' => "/purchasing/{$poId}"
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Failed to create PO: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show PO Details
     */
    public function show(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $id = (int)$request->param('id');
        $po = $this->poModel->getDetails($id);

        if (!$po) {
            return $this->redirect('/purchasing');
        }

        return $this->render('purchasing/show', [
            'title' => "{$po['po_number']} - PO Details | MediCore ERP",
            'user' => $this->auth->user(),
            'po' => $po
        ]);
    }

    /**
     * Printable Official BPOM Surat Pesanan
     */
    public function printSp(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $id = (int)$request->param('id');
        $po = $this->poModel->getDetails($id);

        if (!$po) {
            return $this->redirect('/purchasing');
        }

        return $this->render('purchasing/print_sp', [
            'title' => "Surat Pesanan {$po['po_number']}",
            'user' => $this->auth->user(),
            'po' => $po
        ]);
    }

    /**
     * Goods Receipt Form (Penerimaan Barang / Faktur PBF)
     */
    public function receive(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $poId = (int)$request->param('id');
        $po = $this->poModel->getDetails($poId);

        if (!$po) {
            return $this->redirect('/purchasing');
        }

        $nextGrnNumber = $this->grModel->generateGrnNumber();

        return $this->render('purchasing/receive', [
            'title' => "Receive Goods - {$po['po_number']} | MediCore ERP",
            'user' => $this->auth->user(),
            'po' => $po,
            'nextGrnNumber' => $nextGrnNumber
        ]);
    }

    /**
     * Store Goods Receipt (GRN), Increment Batches & Update PO Status
     */
    public function storeReceive(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $user = $this->auth->user();
        $data = json_decode($request->getBody(), true) ?: $request->all();

        if (empty($data['supplier_id']) || empty($data['invoice_number']) || empty($data['items'])) {
            return $this->json(['success' => false, 'message' => 'Invoice number, supplier and received items are required.'], 422);
        }

        try {
            $grData = [
                'grn_number' => $this->grModel->generateGrnNumber(),
                'purchase_order_id' => !empty($data['purchase_order_id']) ? (int)$data['purchase_order_id'] : null,
                'supplier_id' => (int)$data['supplier_id'],
                'received_by' => (int)$user['id'],
                'invoice_number' => trim($data['invoice_number']),
                'invoice_date' => $data['invoice_date'] ?? date('Y-m-d'),
                'due_date' => $data['due_date'] ?? date('Y-m-d', strtotime('+30 days')),
                'subtotal' => (float)($data['subtotal'] ?? 0),
                'tax_amount' => (float)($data['tax_amount'] ?? 0),
                'total_amount' => (float)($data['total_amount'] ?? 0),
                'notes' => $data['notes'] ?? null
            ];

            $grId = $this->grModel->receiveGoods($grData, $data['items']);

            return $this->json([
                'success' => true,
                'message' => "Goods Receipt {$grData['grn_number']} processed! Stock & FEFO batches updated.",
                'gr_id' => $grId,
                'redirect_url' => !empty($data['purchase_order_id']) ? "/purchasing/{$data['purchase_order_id']}" : "/purchasing/ap-ledger"
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Receiving failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Accounts Payable (AP Ledger) & Debt Management Overview
     */
    public function apLedger(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $paymentStatus = $request->get('payment_status', 'all');
        $overdueOnly = (bool)$request->get('overdue', false);

        $invoices = $this->grModel->getList($paymentStatus, $overdueOnly);
        $summary = $this->grModel->getApSummary();

        return $this->render('purchasing/ap_ledger', [
            'title' => 'Accounts Payable (PBF Debt Ledger) | MediCore ERP',
            'user' => $this->auth->user(),
            'invoices' => $invoices,
            'summary' => $summary,
            'currentPaymentStatus' => $paymentStatus,
            'isOverdueOnly' => $overdueOnly
        ]);
    }

    /**
     * Record payment towards PBF Invoice
     */
    public function recordPayment(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $user = $this->auth->user();
        $data = json_decode($request->getBody(), true) ?: $request->all();

        $grId = (int)($data['goods_receipt_id'] ?? 0);
        $amount = (float)($data['amount_paid'] ?? 0);
        $paymentMethod = $data['payment_method'] ?? 'bank_transfer';
        $refNumber = $data['reference_number'] ?? null;
        $notes = $data['notes'] ?? null;

        if ($grId <= 0 || $amount <= 0) {
            return $this->json(['success' => false, 'message' => 'Valid invoice ID and payment amount are required.'], 422);
        }

        try {
            $success = $this->grModel->recordPayment($grId, $amount, $paymentMethod, $refNumber, $notes, (int)$user['id']);

            if ($success) {
                return $this->json([
                    'success' => true,
                    'message' => 'Payment of Rp ' . number_format($amount, 0, ',', '.') . ' recorded successfully.'
                ]);
            } else {
                return $this->json(['success' => false, 'message' => 'Invoice not found.'], 404);
            }
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }
}
