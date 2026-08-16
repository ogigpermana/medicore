<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Batch;

/**
 * Product & Inventory Controller
 * Manages medication catalog, barcode lookups, FEFO batch allocations, and stock status
 */
class ProductController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;
    private Unit $unitModel;
    private Supplier $supplierModel;
    private Batch $batchModel;
    private Auth $auth;

    public function __construct(Request $request, \Core\Container $container)
    {
        parent::__construct($request, $container);
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->unitModel = new Unit();
        $this->supplierModel = new Supplier();
        $this->batchModel = new Batch();
        $this->auth = new Auth($this->session);
    }

    /**
     * Display medication catalog view or return JSON if requested via AJAX
     */
    public function index(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $filters = [
            'search' => $request->query('q'),
            'category_id' => $request->query('category_id'),
            'low_stock' => $request->query('low_stock') === '1'
        ];

        $page = (int)$request->query('page', 1);
        $perPage = (int)$request->query('per_page', 25);

        $paginated = $this->productModel->getCatalogPaginated($filters, $page, $perPage);
        $products = $paginated['items'];
        $categories = $this->categoryModel->getActiveWithCounts();
        $units = $this->unitModel->all();
        $suppliers = $this->supplierModel->getActive();
        $currentUser = $this->auth->user();
        $role = strtolower($currentUser['role_name'] ?? $currentUser['role'] ?? 'pharmacist');

        if ($request->header('Accept') === 'application/json' || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return $this->json([
                'success' => true,
                'data' => $products,
                'pagination' => $paginated
            ]);
        }

        return $this->view('inventory.index', [
            'title' => 'Medication & Inventory Catalog — MediCore',
            'user' => $currentUser,
            'role' => $role,
            'activeMenu' => 'products',
            'products' => $products,
            'pagination' => $paginated,
            'categories' => $categories,
            'units' => $units,
            'suppliers' => $suppliers,
            'filters' => $filters
        ]);
    }

    /**
     * Store new medication in catalog
     */
    public function store(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = $request->all();

        // Validation
        if (empty($data['name']) || empty($data['sku']) || !isset($data['sell_price'])) {
            return $this->json([
                'success' => false,
                'message' => 'Product Name, SKU, and Selling Price are required.'
            ], 422);
        }

        // Check SKU uniqueness
        if ($this->productModel->findBySku($data['sku'])) {
            return $this->json([
                'success' => false,
                'message' => 'A product with this SKU already exists.'
            ], 409);
        }

        try {
            $productId = $this->productModel->create([
                'sku' => strtoupper(trim($data['sku'])),
                'barcode' => !empty($data['barcode']) ? trim($data['barcode']) : null,
                'name' => trim($data['name']),
                'generic_name' => trim($data['generic_name'] ?? ''),
                'category_id' => !empty($data['category_id']) ? (int)$data['category_id'] : null,
                'unit_id' => !empty($data['unit_id']) ? (int)$data['unit_id'] : null,
                'dosage' => trim($data['dosage'] ?? ''),
                'manufacturer' => trim($data['manufacturer'] ?? ''),
                'buy_price' => (float)($data['buy_price'] ?? 0),
                'sell_price' => (float)$data['sell_price'],
                'min_stock' => (int)($data['min_stock'] ?? 10),
                'stock_quantity' => (int)($data['initial_stock'] ?? 0),
                'requires_prescription' => !empty($data['requires_prescription']) ? 1 : 0,
                'is_active' => 1
            ]);

            // If initial batch is provided, record it
            if (!empty($data['batch_number']) && !empty($data['expiry_date']) && !empty($data['initial_stock'])) {
                $this->batchModel->create([
                    'product_id' => $productId,
                    'batch_number' => strtoupper(trim($data['batch_number'])),
                    'expiry_date' => $data['expiry_date'],
                    'initial_quantity' => (int)$data['initial_stock'],
                    'current_quantity' => (int)$data['initial_stock'],
                    'buy_price' => (float)($data['buy_price'] ?? 0),
                    'supplier_id' => !empty($data['supplier_id']) ? (int)$data['supplier_id'] : null,
                    'received_date' => date('Y-m-d')
                ]);
            }

            return $this->json([
                'success' => true,
                'message' => 'Medication successfully added to catalog.',
                'product_id' => $productId
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get FEFO batches for a specific product
     */
    public function getBatches(Request $request): Response
    {
        $productId = (int)$request->query('product_id');

        if (!$productId) {
            return $this->json(['success' => false, 'message' => 'Product ID is required'], 400);
        }

        $batches = $this->batchModel->getFefoBatches($productId);

        return $this->json([
            'success' => true,
            'batches' => $batches
        ]);
    }

    /**
     * Display FEFO Expiry Sentinel View
     */
    public function fefoSentinel(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $days = (int)($request->query('days') ?? 60);
        $expiringBatches = $this->batchModel->getExpiringBatches($days);
        $currentUser = $this->auth->user();
        $role = strtolower($currentUser['role_name'] ?? $currentUser['role'] ?? 'pharmacist');

        return $this->view('inventory.fefo', [
            'title' => 'FEFO Expiry Sentinel — MediCore',
            'user' => $currentUser,
            'role' => $role,
            'activeMenu' => 'fefo',
            'batches' => $expiringBatches,
            'daysThreshold' => $days
        ]);
    }

    /**
     * Fast barcode/SKU lookup API (Used by POS scanner)
     */
    public function lookup(Request $request): Response
    {
        $code = trim($request->query('code') ?? '');

        if (empty($code)) {
            return $this->json(['success' => false, 'message' => 'Code parameter is required'], 400);
        }

        $product = $this->productModel->findByBarcode($code) ?? $this->productModel->findBySku($code);

        if (!$product) {
            return $this->json(['success' => false, 'message' => 'Medication not found'], 404);
        }

        $fefoBatches = $this->batchModel->getFefoBatches($product['id']);

        return $this->json([
            'success' => true,
            'product' => $product,
            'fefo_batches' => $fefoBatches
        ]);
    }

    /**
     * Download CSV Import Template
     */
    public function downloadTemplate(Request $request): Response
    {
        $headers = [
            'SKU', 'Barcode', 'Name', 'GenericName', 'CategorySlug', 'UnitSymbol',
            'Dosage', 'Manufacturer', 'BuyPrice', 'SellPrice', 'MinStock', 'StockQuantity',
            'RequiresPrescription', 'BatchNumber', 'ExpiryDate'
        ];

        $sampleRow = [
            'MED-AMX-500', '8991001234511', 'Amoxicillin 500mg', 'Amoxicillin Trihydrate',
            'antibiotics', 'str', '500 mg', 'Kimia Farma', '14000', '18500', '20', '100', '1',
            'LOT-2026-X01', date('Y-m-d', strtotime('+12 months'))
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        fputcsv($output, $sampleRow);
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return new Response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="medicore_medication_template.csv"'
        ]);
    }

    /**
     * Import Medications from CSV file / text
     */
    public function importCsv(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $csvContent = null;

        // Check if file was uploaded
        if (!empty($_FILES['csv_file']['tmp_name']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
            $csvContent = file_get_contents($_FILES['csv_file']['tmp_name']);
        } elseif (!empty($request->input('csv_data'))) {
            $csvContent = $request->input('csv_data');
        }

        if (empty($csvContent)) {
            return $this->json(['success' => false, 'message' => 'No CSV file or data provided.'], 422);
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($csvContent));
        if (count($lines) < 2) {
            return $this->json(['success' => false, 'message' => 'CSV must contain a header and at least one data row.'], 422);
        }

        // Cache categories and units
        $allCategories = $this->categoryModel->all();
        $catMap = [];
        foreach ($allCategories as $c) {
            $catMap[strtolower($c['slug'])] = $c['id'];
            $catMap[strtolower($c['name'])] = $c['id'];
        }

        $allUnits = $this->unitModel->all();
        $unitMap = [];
        foreach ($allUnits as $u) {
            $unitMap[strtolower($u['symbol'])] = $u['id'];
            $unitMap[strtolower($u['name'])] = $u['id'];
        }

        $imported = 0;
        $updated = 0;
        $errors = [];

        // Parse header
        $header = str_getcsv(array_shift($lines));
        $header = array_map(function($h) { return strtolower(trim(str_replace([' ', '_'], '', $h))); }, $header);

        foreach ($lines as $lineIndex => $line) {
            if (empty(trim($line))) continue;

            $row = str_getcsv($line);
            if (count($row) < 3) continue;

            $data = [];
            foreach ($header as $idx => $key) {
                $data[$key] = $row[$idx] ?? '';
            }

            $sku = strtoupper(trim($data['sku'] ?? ''));
            $name = trim($data['name'] ?? '');
            $sellPrice = (float)($data['sellprice'] ?? 0);

            if (empty($sku) || empty($name) || $sellPrice <= 0) {
                $errors[] = "Row #" . ($lineIndex + 2) . ": SKU, Name, and Sell Price are required.";
                continue;
            }

            $catSlug = strtolower(trim($data['categoryslug'] ?? ''));
            $categoryId = $catMap[$catSlug] ?? null;

            $unitSymbol = strtolower(trim($data['unitsymbol'] ?? ''));
            $unitId = $unitMap[$unitSymbol] ?? null;

            $productData = [
                'sku' => $sku,
                'barcode' => !empty($data['barcode']) ? trim($data['barcode']) : null,
                'name' => $name,
                'generic_name' => trim($data['genericname'] ?? ''),
                'category_id' => $categoryId,
                'unit_id' => $unitId,
                'dosage' => trim($data['dosage'] ?? ''),
                'manufacturer' => trim($data['manufacturer'] ?? ''),
                'buy_price' => (float)($data['buyprice'] ?? 0),
                'sell_price' => $sellPrice,
                'min_stock' => (int)($data['minstock'] ?? 10),
                'stock_quantity' => (int)($data['stockquantity'] ?? 0),
                'requires_prescription' => !empty($data['requiresprescription']) ? 1 : 0,
                'is_active' => 1
            ];

            try {
                $existing = $this->productModel->findBySku($sku) 
                    ?? (!empty($productData['barcode']) ? $this->productModel->findByBarcode($productData['barcode']) : null);

                if ($existing) {
                    $this->productModel->update($existing['id'], $productData);
                    $productId = $existing['id'];
                    $updated++;
                } else {
                    $productId = $this->productModel->create($productData);
                    $imported++;
                }

                // Batch Lot allocation if present
                if (!empty($data['batchnumber']) && !empty($data['expirydate']) && !empty($data['stockquantity'])) {
                    $this->batchModel->create([
                        'product_id' => $productId,
                        'batch_number' => strtoupper(trim($data['batchnumber'])),
                        'expiry_date' => $data['expirydate'],
                        'initial_quantity' => (int)$data['stockquantity'],
                        'current_quantity' => (int)$data['stockquantity'],
                        'buy_price' => (float)($data['buyprice'] ?? 0),
                        'received_date' => date('Y-m-d')
                    ]);
                }
            } catch (\Exception $e) {
                $errors[] = "Row #" . ($lineIndex + 2) . " ({$sku}): " . $e->getMessage();
            }
        }

        return $this->json([
            'success' => true,
            'message' => "Bulk import completed. {$imported} medications created, {$updated} updated.",
            'imported' => $imported,
            'updated' => $updated,
            'errors' => $errors
        ]);
    }
}
