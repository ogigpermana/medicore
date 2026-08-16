<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\CashierShift;

/**
 * Point of Sale (POS) & Billing Controller
 * Handles cashier terminal, barcode scanning, shift management, checkout, and receipt generation
 */
class PosController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;
    private Sale $saleModel;
    private CashierShift $shiftModel;
    private Auth $auth;

    public function __construct(Request $request, \Core\Container $container)
    {
        parent::__construct($request, $container);
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->saleModel = new Sale();
        $this->shiftModel = new CashierShift();
        $this->auth = new Auth($this->session);
    }

    /**
     * Display POS Checkout Terminal
     */
    public function index(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $currentUser = $this->auth->user();
        $role = strtolower($currentUser['role_name'] ?? $currentUser['role'] ?? 'cashier');
        $products = $this->productModel->getCatalog();
        $categories = $this->categoryModel->getActiveWithCounts();
        $activeShift = $this->shiftModel->getActiveShift($currentUser['id']);

        return $this->view('pos.index', [
            'title' => 'POS Terminal — MediCore Pharmacy',
            'user' => $currentUser,
            'role' => $role,
            'activeMenu' => 'pos',
            'products' => $products,
            'categories' => $categories,
            'activeShift' => $activeShift
        ]);
    }

    /**
     * Execute POS Checkout via AJAX
     */
    public function checkout(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $currentUser = $this->auth->user();
        $data = $request->all();

        if (empty($data['items']) || !is_array($data['items'])) {
            return $this->json(['success' => false, 'message' => 'Cart is empty. Please add medications before charging.'], 422);
        }

        $saleData = [
            'user_id' => $currentUser['id'],
            'customer_name' => !empty($data['customer_name']) ? trim($data['customer_name']) : 'General Walk-in Patient',
            'customer_phone' => !empty($data['customer_phone']) ? trim($data['customer_phone']) : null,
            'discount_amount' => (float)($data['discount_amount'] ?? 0),
            'include_tax' => !empty($data['include_tax']) ? 1 : 0,
            'payment_method' => strtolower($data['payment_method'] ?? 'cash'),
            'cash_tendered' => (float)($data['cash_tendered'] ?? 0)
        ];

        try {
            $saleId = $this->saleModel->processCheckout($saleData, $data['items']);
            $sale = $this->saleModel->getSaleDetails($saleId);

            return $this->json([
                'success' => true,
                'message' => 'Sale processed successfully.',
                'data' => $sale
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display Sales History
     */
    public function history(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $currentUser = $this->auth->user();
        $role = strtolower($currentUser['role_name'] ?? $currentUser['role'] ?? 'cashier');

        $filters = [
            'search' => $request->query('q'),
            'payment_method' => $request->query('payment_method', 'all'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date')
        ];

        $page = (int)$request->query('page', 1);
        $perPage = (int)$request->query('per_page', 25);

        $paginated = $this->saleModel->getSalesPaginated($filters, $page, $perPage);
        $sales = $paginated['items'];

        if ($request->header('Accept') === 'application/json' || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return $this->json([
                'success' => true,
                'data' => $sales,
                'pagination' => $paginated
            ]);
        }

        return $this->view('pos.history', [
            'title' => 'Sales & Transaction History — MediCore',
            'user' => $currentUser,
            'role' => $role,
            'activeMenu' => 'pos_history',
            'sales' => $sales,
            'pagination' => $paginated,
            'filters' => $filters
        ]);
    }

    /**
     * Display printable receipt
     */
    public function receipt(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $id = (int)$request->param('id');
        $sale = $this->saleModel->getWithDetails($id);

        if (!$sale) {
            return new Response('Transaction not found', 404);
        }

        return $this->view('pos.receipt', [
            'title' => "Receipt #{$sale['invoice_number']} — MediCore",
            'sale' => $sale
        ]);
    }

    /**
     * Open Cashier Shift Drawer
     */
    public function openShift(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $currentUser = $this->auth->user();
        $openingCash = (float)($request->input('opening_cash') ?? 0);
        $notes = $request->input('notes');

        $shiftId = $this->shiftModel->openShift($currentUser['id'], $openingCash, $notes);

        return $this->json([
            'success' => true,
            'message' => 'Cashier shift opened successfully.',
            'shift_id' => $shiftId
        ]);
    }

    /**
     * Close Cashier Shift Drawer
     */
    public function closeShift(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $currentUser = $this->auth->user();
        $activeShift = $this->shiftModel->getActiveShift($currentUser['id']);

        if (!$activeShift) {
            return $this->json(['success' => false, 'message' => 'No active shift found to close.'], 404);
        }

        $closingCash = (float)($request->input('closing_cash') ?? 0);
        $notes = $request->input('notes');

        $closed = $this->shiftModel->closeShift($activeShift['id'], $closingCash, $notes);

        return $this->json([
            'success' => $closed,
            'message' => $closed ? 'Shift closed and drawer reconciled successfully.' : 'Failed to close shift.'
        ]);
    }
}
