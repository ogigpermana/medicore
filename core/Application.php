<?php

namespace Core;

/**
 * Application Class
 * Main bootstrap class for the MediCore Framework
 */

class Application
{
    private static ?Application $instance = null;
    private Router $router;
    private Container $container;
    private Config $config;
    private array $middleware = [];

    protected function __construct()
    {
        $this->container = new Container();
        $this->config = new Config();
        $this->router = new Router($this->container);
        $this->bootstrap();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): Application
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Create new instance (for testing)
     */
    public static function create(): Application
    {
        return new self();
    }

    /**
     * Bootstrap the application
     */
    private function bootstrap(): void
    {
        // Load configuration
        $this->config->load(__DIR__ . '/../config');

        // Set error reporting
        $this->setErrorReporting();

        // Set timezone
        date_default_timezone_set($this->config->get('app.timezone', 'Asia/Jakarta'));

        // Configure session
        $this->configureSession();

        // Initialize database
        $this->container->singleton(Database::class, function () {
            return new Database($this->config->get('database'));
        });

        // Initialize session
        $this->container->singleton(Session::class, function () {
            return new Session();
        });

        // Initialize logger
        $this->container->singleton(Logger::class, function () {
            return new Logger($this->config->get('app.log_path', __DIR__ . '/../storage/logs'));
        });

        // Initialize CSRF middleware
        $this->container->singleton(\App\Middleware\CsrfMiddleware::class, function () {
            return new \App\Middleware\CsrfMiddleware();
        });

        // Initialize Auth middleware
        $this->container->singleton(\App\Middleware\AuthMiddleware::class, function () {
            return new \App\Middleware\AuthMiddleware();
        });

        // Initialize Audit Logger
        $this->container->singleton(\Core\AuditLogger::class, function () {
            return new \Core\AuditLogger(
                $this->container->get(Database::class),
                $this->container->get(Session::class)
            );
        });

        // Initialize Auth
        $this->container->singleton(\Core\Auth::class, function () {
            return new \Core\Auth($this->container->get(Session::class));
        });

        // Initialize RateLimiter
        $this->container->singleton(\Core\RateLimiter::class, function () {
            return new \Core\RateLimiter(5, 15);
        });

        // Initialize RememberMe
        $this->container->singleton(\Core\RememberMe::class, function () {
            return new \Core\RememberMe(
                $this->container->get(Database::class),
                $this->container->get(Session::class)
            );
        });

        // Initialize EmailService
        $this->container->singleton(\Core\EmailService::class, function () {
            $emailConfig = require __DIR__ . '/../config/email.php';
            return new \Core\EmailService($emailConfig);
        });

        // Initialize User model
        $this->container->singleton(\App\Models\User::class, function () {
            return new \App\Models\User();
        });

        // Initialize View (must be before Response to avoid type confusion)
        $this->container->clearInstance(\Core\View::class);
        $this->container->clearInstance(\Core\Response::class);
        
        $this->container->singleton(\Core\View::class, function () {
            return new \Core\View();
        });

        $this->container->singleton(\Core\Response::class, function () {
            return new \Core\Response();
        });

        // Load routes
        $this->loadRoutes();

        // Load middleware
        $this->loadMiddleware();
    }

    /**
     * Configure session settings
     */
    private function configureSession(): void
    {
        $sessionConfig = $this->config->get('app.session', []);
        
        ini_set('session.gc_maxlifetime', $sessionConfig['lifetime'] ?? 7200);
        ini_set('session.cookie_lifetime', $sessionConfig['lifetime'] ?? 7200);
        ini_set('session.cookie_httponly', $sessionConfig['httponly'] ?? '1');
        ini_set('session.cookie_secure', $sessionConfig['secure'] ?? '0');
        ini_set('session.cookie_samesite', $sessionConfig['samesite'] ?? 'Lax');
        
        if (isset($sessionConfig['cookie'])) {
            session_name($sessionConfig['cookie']);
        }
    }

    /**
     * Set error reporting based on environment
     */
    private function setErrorReporting(): void
    {
        if ($this->config->get('app.debug', false)) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
        }
    }

    /**
     * Load routes from configuration file
     */
    private function loadRoutes(): void
    {
        // Auth routes (without middleware for now)
        $this->router->get('/login', [\App\Controllers\AuthController::class, 'showLogin']);
        $this->router->post('/login', [\App\Controllers\AuthController::class, 'login']);
        $this->router->get('/register', [\App\Controllers\AuthController::class, 'showRegister']);
        $this->router->post('/register', [\App\Controllers\AuthController::class, 'register']);
        $this->router->get('/logout', [\App\Controllers\AuthController::class, 'logout']);
        $this->router->post('/logout', [\App\Controllers\AuthController::class, 'logout']);
        $this->router->get('/api/me', [\App\Controllers\AuthController::class, 'me']);

        // Profile routes
        $this->router->get('/profile', [\App\Controllers\ProfileController::class, 'show']);
        $this->router->post('/profile', [\App\Controllers\ProfileController::class, 'update']);
        $this->router->get('/api/profile', [\App\Controllers\ProfileController::class, 'getProfile']);

        // Change password routes
        $this->router->get('/change-password', [\App\Controllers\ProfileController::class, 'showChangePassword']);
        $this->router->post('/change-password', [\App\Controllers\ProfileController::class, 'changePassword']);

        // Password reset routes
        $this->router->get('/forgot-password', [\App\Controllers\AuthController::class, 'showForgotPassword']);
        $this->router->post('/forgot-password', [\App\Controllers\AuthController::class, 'sendPasswordReset']);
        $this->router->get('/reset-password/{token}', [\App\Controllers\AuthController::class, 'showResetPassword']);
        $this->router->post('/reset-password/{token}', [\App\Controllers\AuthController::class, 'resetPassword']);

        // Email verification routes
        $this->router->get('/verify-email/{token}', [\App\Controllers\AuthController::class, 'verifyEmail']);
        $this->router->post('/resend-verification', [\App\Controllers\AuthController::class, 'resendVerification']);

        // REST API: Stateless JWT Authentication
        $this->router->post('/api/auth/login', [\App\Controllers\Api\AuthController::class, 'login']);
        $this->router->post('/api/auth/refresh', [\App\Controllers\Api\AuthController::class, 'refresh']);
        $this->router->get('/api/auth/me', [\App\Controllers\Api\AuthController::class, 'me'])
                     ->middleware(\App\Middleware\JwtAuthMiddleware::class);

        // Landing page route
        $this->router->get('/', [\App\Controllers\LandingController::class, 'index']);

        // Dashboard route
        $this->router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index']);

        // Module 2: Products & Inventory routes
        $this->router->get('/inventory/products', [\App\Controllers\ProductController::class, 'index']);
        $this->router->post('/inventory/products', [\App\Controllers\ProductController::class, 'store']);
        $this->router->get('/inventory/products/template', [\App\Controllers\ProductController::class, 'downloadTemplate']);
        $this->router->post('/inventory/products/import', [\App\Controllers\ProductController::class, 'importCsv']);
        $this->router->get('/inventory/fefo', [\App\Controllers\ProductController::class, 'fefoSentinel']);
        $this->router->get('/inventory/categories', [\App\Controllers\CategoryController::class, 'index']);
        $this->router->post('/inventory/categories', [\App\Controllers\CategoryController::class, 'store']);
        $this->router->post('/inventory/categories/update', [\App\Controllers\CategoryController::class, 'update']);
        $this->router->post('/inventory/categories/delete', [\App\Controllers\CategoryController::class, 'delete']);
        $this->router->get('/inventory/suppliers', [\App\Controllers\SupplierController::class, 'index']);
        $this->router->post('/inventory/suppliers', [\App\Controllers\SupplierController::class, 'store']);
        $this->router->post('/inventory/suppliers/update', [\App\Controllers\SupplierController::class, 'update']);
        $this->router->post('/inventory/suppliers/delete', [\App\Controllers\SupplierController::class, 'delete']);
        $this->router->get('/api/suppliers/lookup', [\App\Controllers\SupplierController::class, 'lookup']);
        $this->router->get('/api/inventory/lookup', [\App\Controllers\ProductController::class, 'lookup']);
        $this->router->get('/api/inventory/batches', [\App\Controllers\ProductController::class, 'getBatches']);

        // Module: CRM & Patient Master Records
        $this->router->get('/crm/customers', [\App\Controllers\CustomerController::class, 'index']);
        $this->router->post('/crm/customers', [\App\Controllers\CustomerController::class, 'store']);
        $this->router->post('/crm/customers/update', [\App\Controllers\CustomerController::class, 'update']);
        $this->router->post('/crm/customers/delete', [\App\Controllers\CustomerController::class, 'delete']);
        $this->router->get('/crm/customers/{id}', [\App\Controllers\CustomerController::class, 'show']);
        $this->router->get('/api/crm/customers/lookup', [\App\Controllers\CustomerController::class, 'lookup']);

        // Module 3 / Milestone 5: POS & Billing routes
        $this->router->get('/pos', [\App\Controllers\PosController::class, 'index']);
        $this->router->post('/pos/checkout', [\App\Controllers\PosController::class, 'checkout']);
        $this->router->get('/pos/history', [\App\Controllers\PosController::class, 'history']);
        $this->router->get('/pos/receipt/{id}', [\App\Controllers\PosController::class, 'receipt']);
        $this->router->post('/pos/shift/open', [\App\Controllers\PosController::class, 'openShift']);
        $this->router->post('/pos/shift/close', [\App\Controllers\PosController::class, 'closeShift']);

        // Module 4 / Milestone 6A: Clinical Prescription Management
        $this->router->get('/prescriptions', [\App\Controllers\PrescriptionController::class, 'index']);
        $this->router->get('/prescriptions/create', [\App\Controllers\PrescriptionController::class, 'create']);
        $this->router->post('/prescriptions', [\App\Controllers\PrescriptionController::class, 'store']);
        $this->router->get('/prescriptions/{id}', [\App\Controllers\PrescriptionController::class, 'show']);
        $this->router->post('/prescriptions/review', [\App\Controllers\PrescriptionController::class, 'review']);
        $this->router->post('/prescriptions/status', [\App\Controllers\PrescriptionController::class, 'updateStatus']);
        $this->router->get('/prescriptions/{id}/label', [\App\Controllers\PrescriptionController::class, 'label']);
        $this->router->get('/api/prescriptions/lookup', [\App\Controllers\PrescriptionController::class, 'posLookup']);

        // Module 5 / Milestone 6B: Purchasing, PBF Purchase Orders, GRN & AP Ledger
        $this->router->get('/purchasing', [\App\Controllers\PurchasingController::class, 'index']);
        $this->router->get('/purchasing/create', [\App\Controllers\PurchasingController::class, 'create']);
        $this->router->post('/purchasing', [\App\Controllers\PurchasingController::class, 'store']);
        $this->router->get('/purchasing/ap-ledger', [\App\Controllers\PurchasingController::class, 'apLedger']);
        $this->router->post('/purchasing/receive', [\App\Controllers\PurchasingController::class, 'storeReceive']);
        $this->router->post('/purchasing/pay-invoice', [\App\Controllers\PurchasingController::class, 'recordPayment']);
        $this->router->get('/purchasing/{id}', [\App\Controllers\PurchasingController::class, 'show']);
        $this->router->get('/purchasing/{id}/print-sp', [\App\Controllers\PurchasingController::class, 'printSp']);
        $this->router->get('/purchasing/{id}/receive', [\App\Controllers\PurchasingController::class, 'receive']);

        // Module 6 / Milestone 7: Financial Reports & Physical Stock Opname
        $this->router->get('/reports', [\App\Controllers\ReportController::class, 'index']);
        $this->router->get('/reports/profit-loss', [\App\Controllers\ReportController::class, 'profitLoss']);
        $this->router->get('/reports/sales', [\App\Controllers\ReportController::class, 'salesSummary']);
        $this->router->get('/reports/inventory', [\App\Controllers\ReportController::class, 'inventoryValuation']);
        $this->router->get('/audit-logs', [\App\Controllers\AuditLogController::class, 'index']);
        $this->router->get('/audit-logs/export', [\App\Controllers\AuditLogController::class, 'export']);

        $this->router->get('/stock-opname', [\App\Controllers\StockOpnameController::class, 'index']);
        $this->router->get('/stock-opname/create', [\App\Controllers\StockOpnameController::class, 'create']);
        $this->router->post('/stock-opname', [\App\Controllers\StockOpnameController::class, 'store']);
        $this->router->get('/stock-opname/{id}', [\App\Controllers\StockOpnameController::class, 'show']);
        $this->router->get('/stock-opname/{id}/count', [\App\Controllers\StockOpnameController::class, 'count']);
        $this->router->post('/stock-opname/save-counts', [\App\Controllers\StockOpnameController::class, 'saveCounts']);
        $this->router->post('/stock-opname/approve', [\App\Controllers\StockOpnameController::class, 'approve']);

        // Module 7 / Milestone 8: Multi-Branch & Inter-Warehouse Stock Transfers
        $this->router->get('/transfers', [\App\Controllers\TransferController::class, 'index']);
        $this->router->get('/transfers/create', [\App\Controllers\TransferController::class, 'create']);
        $this->router->post('/transfers', [\App\Controllers\TransferController::class, 'store']);
        $this->router->get('/transfers/{id}', [\App\Controllers\TransferController::class, 'show']);
        $this->router->get('/transfers/{id}/delivery-note', [\App\Controllers\TransferController::class, 'printDeliveryNote']);
        $this->router->get('/transfers/{id}/print-surat-jalan', [\App\Controllers\TransferController::class, 'printSuratJalan']);
        $this->router->post('/transfers/dispatch', [\App\Controllers\TransferController::class, 'dispatch']);
        $this->router->post('/transfers/receive', [\App\Controllers\TransferController::class, 'receive']);

        $this->router->get('/api/health', function ($request) {
            return new Response(json_encode(['status' => 'ok', 'message' => 'System is running']), 200, ['Content-Type' => 'application/json']);
        });
    }

    /**
     * Load middleware from configuration file
     */
    private function loadMiddleware(): void
    {
        $middlewareFile = __DIR__ . '/../config/middleware.php';
        if (file_exists($middlewareFile)) {
            $this->middleware = require $middlewareFile;
        }
    }

    /**
     * Run the application
     */
    public function run(): void
    {
        try {
            $request = Request::capture();
            $request->setContainer($this->container);
            $response = $this->router->dispatch($request);
            $response->send();
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Handle exceptions
     */
    private function handleException(\Exception $e): void
    {
        // Simple logging without container
        $logMessage = date('Y-m-d H:i:s') . " [ERROR] " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
        @file_put_contents(__DIR__ . '/../storage/logs/' . date('Y-m-d') . '.log', $logMessage . PHP_EOL, FILE_APPEND);

        if ($this->config->get('app.debug', false)) {
            echo "<h1>Error</h1>";
            echo "<p>{$e->getMessage()}</p>";
            echo "<p>File: {$e->getFile()} Line: {$e->getLine()}</p>";
            echo "<pre>{$e->getTraceAsString()}</pre>";
        } else {
            $errorView = __DIR__ . '/../app/Views/errors/500.php';
            if (file_exists($errorView)) {
                http_response_code(500);
                include $errorView;
            } else {
                echo "An internal error occurred. Please try again later.";
            }
        }
    }

    /**
     * Get dependency container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Get router instance
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Get config instance
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}

/**
 * Helper function to get app instance
 */
function app(): Application
{
    return Application::getInstance();
}