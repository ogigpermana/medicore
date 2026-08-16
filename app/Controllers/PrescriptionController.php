<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\Category;

/**
 * Prescription Controller
 * Handles Digital Rx Queue, Clinical Pharmacist Screening, Compounding, Dosage Labels, and POS Handoff
 */
class PrescriptionController extends Controller
{
    private Prescription $prescriptionModel;
    private Product $productModel;
    private Category $categoryModel;
    private Auth $auth;

    public function __construct(Request $request, \Core\Container $container)
    {
        parent::__construct($request, $container);
        $this->prescriptionModel = new Prescription();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->auth = new Auth($this->session);
    }

    /**
     * Display Prescription Queue
     */
    public function index(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $currentUser = $this->auth->user();
        $role = strtolower($currentUser['role_name'] ?? $currentUser['role'] ?? 'pharmacist');

        $status = $request->query('status');
        $search = $request->query('q');

        $filters = [];
        if (!empty($status) && $status !== 'all') {
            $filters['status'] = $status;
        }
        if (!empty($search)) {
            $filters['search'] = $search;
        }

        $prescriptions = $this->prescriptionModel->getQueue($filters);
        $statusCounts = $this->prescriptionModel->getCountsByStatus();

        if ($request->header('Accept') === 'application/json' || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return $this->json([
                'success' => true,
                'data' => $prescriptions,
                'counts' => $statusCounts
            ]);
        }

        return $this->view('prescriptions.index', [
            'title' => 'Clinical Prescription Queue — MediCore',
            'user' => $currentUser,
            'role' => $role,
            'activeMenu' => 'prescriptions',
            'prescriptions' => $prescriptions,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status ?? 'all',
            'search' => $search ?? ''
        ]);
    }

    /**
     * Show New Prescription Entry Form
     */
    public function create(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $currentUser = $this->auth->user();
        $role = strtolower($currentUser['role_name'] ?? $currentUser['role'] ?? 'pharmacist');
        $products = $this->productModel->all();
        $nextRxNumber = $this->prescriptionModel->generateNumber();

        return $this->view('prescriptions.create', [
            'title' => 'Enter Digital Prescription — MediCore',
            'user' => $currentUser,
            'role' => $role,
            'activeMenu' => 'prescriptions',
            'products' => $products,
            'nextRxNumber' => $nextRxNumber
        ]);
    }

    /**
     * Store New Prescription with Finished Drugs and Compounds
     */
    public function store(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = $request->all();

        // Validation
        if (empty($data['patient_name']) || empty($data['doctor_name'])) {
            return $this->json(['success' => false, 'message' => 'Patient name and Doctor name are required.'], 422);
        }

        $items = $data['items'] ?? [];
        $compounds = $data['compounds'] ?? [];

        if (empty($items) && empty($compounds)) {
            return $this->json(['success' => false, 'message' => 'Prescription must contain at least one prescribed drug or compounding mixture.'], 422);
        }

        try {
            $prescriptionId = $this->prescriptionModel->createPrescription($data, $items, $compounds);
            $prescription = $this->prescriptionModel->getDetails($prescriptionId);

            return $this->json([
                'success' => true,
                'message' => 'Prescription ' . $prescription['prescription_number'] . ' registered successfully.',
                'prescription_id' => $prescriptionId,
                'prescription_number' => $prescription['prescription_number']
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to save prescription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Detailed Prescription Review View
     */
    public function show(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $id = (int)$request->param('id');
        $prescription = $this->prescriptionModel->getDetails($id);

        if (!$prescription) {
            return $this->redirect('/prescriptions');
        }

        $currentUser = $this->auth->user();
        $role = strtolower($currentUser['role_name'] ?? $currentUser['role'] ?? 'pharmacist');

        return $this->view('prescriptions.show', [
            'title' => 'Prescription ' . $prescription['prescription_number'] . ' — MediCore',
            'user' => $currentUser,
            'role' => $role,
            'activeMenu' => 'prescriptions',
            'prescription' => $prescription
        ]);
    }

    /**
     * Pharmacist Clinical Review & Sign-Off Verification
     */
    public function review(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $currentUser = $this->auth->user();
        $id = (int)$request->input('id');
        $notes = trim($request->input('pharmacist_notes') ?? '');
        $nextStatus = $request->input('next_status') ?? 'reviewed';

        if (empty($id)) {
            return $this->json(['success' => false, 'message' => 'Prescription ID is required.'], 422);
        }

        try {
            $this->prescriptionModel->reviewAndSignOff($id, $currentUser['id'], $notes, $nextStatus);

            return $this->json([
                'success' => true,
                'message' => 'Prescription clinically reviewed and digitally signed by Pharmacist.'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error reviewing prescription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Prescription Status Workflow (e.g. compounding -> ready -> dispensed)
     */
    public function updateStatus(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $id = (int)$request->input('id');
        $status = strtolower(trim($request->input('status') ?? ''));

        $allowedStatuses = ['pending', 'reviewed', 'compounding', 'ready', 'dispensed', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            return $this->json(['success' => false, 'message' => 'Invalid status.'], 422);
        }

        try {
            $this->prescriptionModel->updateStatus($id, $status);

            return $this->json([
                'success' => true,
                'message' => 'Prescription status updated to ' . strtoupper($status) . '.'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Printable Drug Dosage Label (Etiket Obat)
     */
    public function label(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $id = (int)$request->param('id');
        $prescription = $this->prescriptionModel->getDetails($id);

        if (!$prescription) {
            return $this->redirect('/prescriptions');
        }

        $currentUser = $this->auth->user();

        return $this->view('prescriptions.label', [
            'title' => 'Etiket Label — ' . $prescription['prescription_number'],
            'user' => $currentUser,
            'prescription' => $prescription
        ]);
    }

    /**
     * API: Fast Prescription Lookup for POS Cashier Dispatch
     */
    public function posLookup(Request $request): Response
    {
        $code = trim($request->query('code') ?? '');

        if (empty($code)) {
            return $this->json(['success' => false, 'message' => 'Prescription number is required.'], 400);
        }

        $prescription = $this->prescriptionModel->findByNumber($code);

        if (!$prescription) {
            return $this->json(['success' => false, 'message' => 'Prescription not found.'], 404);
        }

        return $this->json([
            'success' => true,
            'prescription' => $prescription
        ]);
    }
}
