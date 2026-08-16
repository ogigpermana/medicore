<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Models\StockOpname;
use App\Models\Category;

class StockOpnameController extends Controller
{
    private StockOpname $opnameModel;
    private Category $categoryModel;
    private Auth $auth;

    public function __construct(Request $request, $container)
    {
        parent::__construct($request, $container);
        $this->opnameModel = new StockOpname();
        $this->categoryModel = new Category();
        $this->auth = $container->get(Auth::class);
    }

    /**
     * Check access permissions for Stock Opname
     */
    private function checkAccess(): ?Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $user = $this->auth->user();
        $role = $user['role_name'] ?? 'cashier';

        if (!in_array($role, ['superadmin', 'owner', 'pharmacist', 'warehouse'])) {
            return $this->redirect('/dashboard');
        }

        return null;
    }

    /**
     * List all Stock Opname sessions
     */
    public function index(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $status = $request->get('status', 'all');
        $sessions = $this->opnameModel->getList($status);

        return $this->render('stock_opname/index', [
            'title' => 'Physical Stock Opname & Audit | MediCore ERP',
            'user' => $this->auth->user(),
            'sessions' => $sessions,
            'currentStatus' => $status
        ]);
    }

    /**
     * Create / Initialize new Stock Opname session
     */
    public function create(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $categories = $this->categoryModel->all();
        $nextNumber = $this->opnameModel->generateNumber();

        return $this->render('stock_opname/create', [
            'title' => 'Start New Stock Opname | MediCore ERP',
            'user' => $this->auth->user(),
            'categories' => $categories,
            'nextNumber' => $nextNumber
        ]);
    }

    /**
     * Store new Stock Opname session
     */
    public function store(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $user = $this->auth->user();
        $data = json_decode($request->getBody(), true) ?: $request->all();

        $title = trim($data['title'] ?? 'Stock Opname ' . date('d F Y'));
        $categoryId = !empty($data['category_id']) ? (int)$data['category_id'] : null;
        $notes = $data['notes'] ?? null;

        try {
            $opnameId = $this->opnameModel->startOpname($title, (int)$user['id'], $categoryId, $notes);

            return $this->json([
                'success' => true,
                'message' => 'Stock opname session initialized successfully.',
                'opname_id' => $opnameId,
                'redirect_url' => "/stock-opname/{$opnameId}/count"
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Failed to initialize: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Physical Count Input Interface (Counting Sheet)
     */
    public function count(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $id = (int)$request->param('id');
        $so = $this->opnameModel->getDetails($id);

        if (!$so) {
            return $this->redirect('/stock-opname');
        }

        return $this->render('stock_opname/count', [
            'title' => "Count Sheet - {$so['opname_number']} | MediCore ERP",
            'user' => $this->auth->user(),
            'so' => $so
        ]);
    }

    /**
     * Save physical count items updates
     */
    public function saveCounts(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $data = json_decode($request->getBody(), true) ?: $request->all();
        $opnameId = (int)($data['stock_opname_id'] ?? 0);
        $items = $data['items'] ?? [];

        if ($opnameId <= 0 || empty($items)) {
            return $this->json(['success' => false, 'message' => 'Items count data is required.'], 422);
        }

        try {
            $this->opnameModel->saveCounts($opnameId, $items);

            return $this->json([
                'success' => true,
                'message' => 'Physical counts and variances saved successfully.',
                'redirect_url' => "/stock-opname/{$opnameId}"
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Save failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show Stock Opname Details & Variance Reconciliation Report
     */
    public function show(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $id = (int)$request->param('id');
        $so = $this->opnameModel->getDetails($id);

        if (!$so) {
            return $this->redirect('/stock-opname');
        }

        return $this->render('stock_opname/show', [
            'title' => "{$so['opname_number']} - Opname Report | MediCore ERP",
            'user' => $this->auth->user(),
            'so' => $so
        ]);
    }

    /**
     * Approve and Auto-Reconcile Physical Inventory
     */
    public function approve(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $user = $this->auth->user();
        $role = $user['role_name'] ?? 'cashier';

        // Only superadmin, owner, pharmacist can approve stock reconciliation
        if (!in_array($role, ['superadmin', 'owner', 'pharmacist'])) {
            return $this->json(['success' => false, 'message' => 'Unauthorized. Supervisor approval required.'], 403);
        }

        $data = json_decode($request->getBody(), true) ?: $request->all();
        $opnameId = (int)($data['stock_opname_id'] ?? 0);

        try {
            $success = $this->opnameModel->approveAndReconcile($opnameId, (int)$user['id']);

            if ($success) {
                return $this->json([
                    'success' => true,
                    'message' => 'Stock opname approved! Physical variances reconciled and stock movements logged.',
                    'redirect_url' => "/stock-opname/{$opnameId}"
                ]);
            } else {
                return $this->json(['success' => false, 'message' => 'Opname session not found or already completed.'], 400);
            }
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Reconciliation failed: ' . $e->getMessage()], 500);
        }
    }
}
