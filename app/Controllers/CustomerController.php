<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Models\Customer;

/**
 * Customer Controller
 * CRM & Patient Master Records with Clinical Allergy Tracking
 */
class CustomerController extends Controller
{
    private Customer $customerModel;
    private Auth $auth;

    public function __construct(Request $request, \Core\Container $container)
    {
        parent::__construct($request, $container);
        $this->customerModel = new Customer();
        $this->auth = $container->get(Auth::class);
    }

    private function checkAccess(): ?Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $user = $this->auth->user();
        $role = strtolower($user['role_name'] ?? $user['role'] ?? 'cashier');

        // All authenticated staff (pharmacist, cashier, superadmin, owner, warehouse) have access to CRM
        if (!in_array($role, ['superadmin', 'owner', 'pharmacist', 'cashier', 'warehouse'])) {
            return $this->redirect('/dashboard');
        }

        return null;
    }

    /**
     * Customer Directory & CRM Dashboard
     */
    public function index(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $search = $request->get('search');
        $filter = $request->get('filter', 'all'); // 'all', 'allergy', 'chronic'
        $status = $request->get('status', 'all');
        $page = (int)$request->get('page', 1);
        $perPage = (int)$request->get('per_page', 25);

        $paginated = $this->customerModel->getCustomersPaginated($search, $filter, $status, $page, $perPage);
        $customers = $paginated['items'];
        $stats = $this->customerModel->getCrmStats();
        $nextCode = $this->customerModel->generateCode();

        if ($request->header('Accept') === 'application/json' || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return $this->json(['success' => true, 'data' => $customers, 'pagination' => $paginated, 'stats' => $stats]);
        }

        return $this->render('crm/index', [
            'title' => 'CRM & Patient Directory | MediCore ERP',
            'user' => $this->auth->user(),
            'customers' => $customers,
            'pagination' => $paginated,
            'stats' => $stats,
            'nextCode' => $nextCode,
            'currentSearch' => $search,
            'currentFilter' => $filter,
            'currentStatus' => $status
        ]);
    }

    /**
     * View Customer / Patient Profile & Medication History
     */
    public function show(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $id = (int)$request->param('id');
        $customer = $this->customerModel->getDetails($id);

        if (!$customer) {
            return $this->redirect('/crm/customers');
        }

        return $this->render('crm/show', [
            'title' => "Patient Profile - {$customer['name']} | MediCore CRM",
            'user' => $this->auth->user(),
            'customer' => $customer
        ]);
    }

    /**
     * Store new customer / patient
     */
    public function store(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $data = $request->all();

        if (empty($data['name'])) {
            return $this->json(['success' => false, 'message' => 'Customer / Patient name is required.'], 422);
        }

        try {
            $id = $this->customerModel->createCustomer($data);
            return $this->json([
                'success' => true,
                'message' => 'Patient ' . htmlspecialchars($data['name']) . ' registered successfully!',
                'id' => $id
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Failed to save customer: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update customer / patient details
     */
    public function update(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $data = $request->all();
        $id = (int)($data['id'] ?? 0);

        if ($id <= 0 || empty($data['name'])) {
            return $this->json(['success' => false, 'message' => 'Valid Customer ID and Name are required.'], 422);
        }

        try {
            $this->customerModel->updateCustomer($id, $data);
            return $this->json([
                'success' => true,
                'message' => 'Patient profile for ' . htmlspecialchars($data['name']) . ' updated successfully!'
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Failed to update customer: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete or Deactivate customer
     */
    public function delete(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $data = $request->all();
        $id = (int)($data['id'] ?? 0);

        if ($id <= 0) {
            return $this->json(['success' => false, 'message' => 'Invalid Customer ID.'], 422);
        }

        try {
            $this->customerModel->deleteCustomer($id);
            return $this->json([
                'success' => true,
                'message' => 'Customer record deleted or deactivated successfully.'
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Failed to delete customer: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API Lookup for POS Register & Prescription creation
     */
    public function lookup(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $query = $request->get('q', '');
        $results = $this->customerModel->search($query, 10);

        return $this->json([
            'success' => true,
            'results' => $results
        ]);
    }
}
