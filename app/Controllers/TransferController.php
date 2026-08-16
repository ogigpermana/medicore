<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Models\StockTransfer;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Batch;

class TransferController extends Controller
{
    private StockTransfer $transferModel;
    private Branch $branchModel;
    private Product $productModel;
    private Batch $batchModel;
    private Auth $auth;

    public function __construct(Request $request, $container)
    {
        parent::__construct($request, $container);
        $this->transferModel = new StockTransfer();
        $this->branchModel = new Branch();
        $this->productModel = new Product();
        $this->batchModel = new Batch();
        $this->auth = $container->get(Auth::class);
    }

    /**
     * Check role access
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
     * List all Stock Transfers
     */
    public function index(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $status = $request->get('status', 'all');
        $transfers = $this->transferModel->getList($status);

        return $this->render('transfers/index', [
            'title' => 'Inter-Branch Stock Transfers | MediCore ERP',
            'user' => $this->auth->user(),
            'transfers' => $transfers,
            'currentStatus' => $status
        ]);
    }

    /**
     * Create New Transfer Form
     */
    public function create(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $branches = $this->branchModel->getActive();
        $products = $this->productModel->all();
        $nextNumber = $this->transferModel->generateNumber();

        return $this->render('transfers/create', [
            'title' => 'New Stock Transfer Request | MediCore ERP',
            'user' => $this->auth->user(),
            'branches' => $branches,
            'products' => $products,
            'nextNumber' => $nextNumber
        ]);
    }

    /**
     * Store New Transfer Request
     */
    public function store(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $user = $this->auth->user();
        $data = json_decode($request->getBody(), true) ?: $request->all();

        $sourceBranchId = (int)($data['source_branch_id'] ?? 0);
        $destBranchId = (int)($data['destination_branch_id'] ?? 0);
        $items = $data['items'] ?? [];

        if ($sourceBranchId <= 0 || $destBranchId <= 0) {
            return $this->json(['success' => false, 'message' => 'Source and destination branches are required.'], 422);
        }

        if ($sourceBranchId === $destBranchId) {
            return $this->json(['success' => false, 'message' => 'Source and destination branches cannot be the same.'], 422);
        }

        if (empty($items)) {
            return $this->json(['success' => false, 'message' => 'At least one medication line item is required.'], 422);
        }

        try {
            $transferData = [
                'source_branch_id' => $sourceBranchId,
                'destination_branch_id' => $destBranchId,
                'requested_by' => (int)$user['id'],
                'status' => 'pending_approval',
                'shipping_notes' => $data['shipping_notes'] ?? null
            ];

            $transferId = $this->transferModel->createTransfer($transferData, $items);

            return $this->json([
                'success' => true,
                'message' => 'Stock transfer request submitted successfully.',
                'transfer_id' => $transferId,
                'redirect_url' => "/transfers/{$transferId}"
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Transfer creation failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show Transfer Details & Status Stepper
     */
    public function show(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $id = (int)$request->param('id');
        $transfer = $this->transferModel->getDetails($id);

        if (!$transfer) {
            return $this->redirect('/transfers');
        }

        return $this->render('transfers/show', [
            'title' => "{$transfer['transfer_number']} - Transfer Details | MediCore ERP",
            'user' => $this->auth->user(),
            'transfer' => $transfer
        ]);
    }

    /**
     * Print Official Delivery Note (Surat Jalan Pengiriman Obat A4)
     */
    /**
     * Print Official Inter-Branch Delivery Note (A4 Document)
     */
    public function printSuratJalan(Request $request): Response
    {
        return $this->printDeliveryNote($request);
    }

    public function printDeliveryNote(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $id = (int)$request->param('id');
        $transfer = $this->transferModel->getDetails($id);

        if (!$transfer) {
            return $this->redirect('/transfers');
        }

        return $this->render('transfers/delivery_note', [
            'title' => "Delivery Note - {$transfer['transfer_number']}",
            'user' => $this->auth->user(),
            'transfer' => $transfer
        ]);
    }

    /**
     * Dispatch Transfer (Kirim Mutasi Stok & Deduct Inventory)
     */
    public function dispatch(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $user = $this->auth->user();
        $role = $user['role_name'] ?? 'cashier';

        if (!in_array($role, ['superadmin', 'owner', 'pharmacist', 'warehouse'])) {
            return $this->json(['success' => false, 'message' => 'Unauthorized to dispatch transfers.'], 403);
        }

        $data = json_decode($request->getBody(), true) ?: $request->all();
        $transferId = (int)($data['transfer_id'] ?? 0);
        $driverName = trim($data['driver_name'] ?? 'Internal Courier');
        $vehicleNumber = trim($data['vehicle_number'] ?? '-');
        $shippingNotes = $data['shipping_notes'] ?? null;

        try {
            $success = $this->transferModel->dispatchTransfer(
                $transferId,
                (int)$user['id'],
                $driverName,
                $vehicleNumber,
                $shippingNotes
            );

            if ($success) {
                return $this->json([
                    'success' => true,
                    'message' => 'Stock transfer dispatched! Items are in-transit and inventory has been deducted.',
                    'redirect_url' => "/transfers/{$transferId}"
                ]);
            } else {
                return $this->json(['success' => false, 'message' => 'Transfer cannot be dispatched in its current status.'], 400);
            }
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Dispatch failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Receive Transfer at Destination Branch
     */
    public function receive(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $user = $this->auth->user();
        $data = json_decode($request->getBody(), true) ?: $request->all();
        $transferId = (int)($data['transfer_id'] ?? 0);
        $items = $data['items'] ?? [];

        if ($transferId <= 0 || empty($items)) {
            return $this->json(['success' => false, 'message' => 'Receipt data is required.'], 422);
        }

        try {
            $success = $this->transferModel->receiveTransfer(
                $transferId,
                (int)$user['id'],
                $items
            );

            if ($success) {
                return $this->json([
                    'success' => true,
                    'message' => 'Transfer received successfully! Stock credited to destination branch inventory.',
                    'redirect_url' => "/transfers/{$transferId}"
                ]);
            } else {
                return $this->json(['success' => false, 'message' => 'Transfer is not in-transit or already completed.'], 400);
            }
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Receipt processing failed: ' . $e->getMessage()], 500);
        }
    }
}
