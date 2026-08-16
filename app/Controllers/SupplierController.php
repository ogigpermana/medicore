<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Models\Supplier;

/**
 * Supplier Controller
 * Complete CRUD management for pharmaceutical distributors (PBF)
 */
class SupplierController extends Controller
{
    private Supplier $supplierModel;
    private Auth $auth;

    public function __construct(Request $request, \Core\Container $container)
    {
        parent::__construct($request, $container);
        $this->supplierModel = new Supplier();
        $this->auth = $container->get(Auth::class);
    }

    private function checkAccess(): ?Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $currentUser = $this->auth->user();
        $role = strtolower($currentUser['role_name'] ?? $currentUser['role'] ?? 'cashier');

        if (!in_array($role, ['superadmin', 'owner', 'pharmacist', 'warehouse'])) {
            return $this->redirect('/dashboard');
        }

        return null;
    }

    /**
     * Supplier Directory & Management Dashboard
     */
    public function index(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $search = $request->get('search');
        $status = $request->get('status', 'all');

        $suppliers = $this->supplierModel->getAllSuppliers($search, $status);
        $nextCode = $this->supplierModel->generateCode();

        if ($request->header('Accept') === 'application/json' || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return $this->json(['success' => true, 'data' => $suppliers]);
        }

        $currentUser = $this->auth->user();
        $role = strtolower($currentUser['role_name'] ?? $currentUser['role'] ?? 'pharmacist');

        return $this->render('inventory/suppliers', [
            'title' => 'Pharmaceutical Suppliers & PBF Directory | MediCore ERP',
            'user' => $currentUser,
            'role' => $role,
            'suppliers' => $suppliers,
            'nextCode' => $nextCode,
            'currentSearch' => $search,
            'currentStatus' => $status
        ]);
    }

    /**
     * Store new Supplier (PBF)
     */
    public function store(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $data = $request->all();

        if (empty($data['name'])) {
            return $this->json(['success' => false, 'message' => 'Supplier / PBF name is required.'], 422);
        }

        try {
            $id = $this->supplierModel->createSupplier($data);
            return $this->json([
                'success' => true,
                'message' => 'Supplier ' . htmlspecialchars($data['name']) . ' registered successfully!',
                'id' => $id
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Failed to create supplier: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update existing Supplier
     */
    public function update(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $data = $request->all();
        $id = (int)($data['id'] ?? 0);

        if ($id <= 0 || empty($data['name'])) {
            return $this->json(['success' => false, 'message' => 'Valid Supplier ID and Name are required.'], 422);
        }

        try {
            $this->supplierModel->updateSupplier($id, $data);
            return $this->json([
                'success' => true,
                'message' => 'Supplier ' . htmlspecialchars($data['name']) . ' updated successfully!'
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Failed to update supplier: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete or Deactivate Supplier
     */
    public function delete(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $data = $request->all();
        $id = (int)($data['id'] ?? 0);

        if ($id <= 0) {
            return $this->json(['success' => false, 'message' => 'Invalid Supplier ID.'], 422);
        }

        try {
            $this->supplierModel->deleteSupplier($id);
            return $this->json([
                'success' => true,
                'message' => 'Supplier removed or deactivated successfully.'
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Failed to delete supplier: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Autocomplete lookup for PO & GRN creation
     */
    public function lookup(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $query = $request->get('q', '');
        $suppliers = $this->supplierModel->getAllSuppliers($query, 'active');

        return $this->json([
            'success' => true,
            'results' => $suppliers
        ]);
    }
}
