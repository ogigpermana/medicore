# Custom PHP Micro Framework Architecture

**Project:** MediCore - Pharmacy Management System  
**Framework Name:** MediCore Framework  
**Version:** 1.0  
**Date:** August 16, 2026

## 1. Framework Overview

**Framework Name:** "MediCore Framework"  
**Type:** Lightweight MVC Micro Framework  
**Philosophy:** Convention over configuration, simplicity, extensibility  
**Design Patterns:** MVC, Front Controller, Dependency Injection, Middleware Pipeline

## 2. Directory Structure

```
medicore-framework/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ProductController.php
│   │   ├── SaleController.php
│   │   ├── ReportController.php
│   │   └── PrescriptionController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Sale.php
│   │   ├── Customer.php
│   │   └── Prescription.php
│   ├── Views/
│   │   ├── layouts/
│   │   │   └── main.php
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── products/
│   │   └── sales/
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   └── CsrfMiddleware.php
│   └── Services/
│       ├── AuthService.php
│       ├── ProductService.php
│       └── SaleService.php
├── core/
│   ├── Application.php
│   ├── Router.php
│   ├── Controller.php
│   ├── Model.php
│   ├── View.php
│   ├── Database.php
│   ├── Request.php
│   ├── Response.php
│   ├── Session.php
│   ├── Auth.php
│   ├── Validator.php
│   ├── Middleware.php
│   ├── Container.php
│   ├── Config.php
│   └── Logger.php
├── config/
│   ├── app.php
│   ├── database.php
│   ├── routes.php
│   └── middleware.php
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
├── storage/
│   ├── logs/
│   ├── cache/
│   └── sessions/
└── vendor/
```

## 3. Core Components

### 3.1 Application.php (Main Bootstrap)

```php
<?php
namespace Core;

class Application {
    private static ?Application $instance = null;
    private Router $router;
    private Container $container;
    private Config $config;
    private array $middleware = [];
    
    private function __construct() {
        $this->container = new Container();
        $this->config = new Config();
        $this->router = new Router($this->container);
        $this->bootstrap();
    }
    
    public static function getInstance(): Application {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function bootstrap(): void {
        // Load configuration
        $this->config->load(__DIR__ . '/../config');
        
        // Set error reporting
        $this->setErrorReporting();
        
        // Set timezone
        date_default_timezone_set($this->config->get('app.timezone', 'Asia/Jakarta'));
        
        // Initialize database
        $this->container->singleton(Database::class, function() {
            return new Database($this->config->get('database'));
        });
        
        // Initialize session
        $this->container->singleton(Session::class, function() {
            return new Session();
        });
        
        // Initialize logger
        $this->container->singleton(Logger::class, function() {
            return new Logger($this->config->get('app.log_path'));
        });
        
        // Load routes
        $this->loadRoutes();
        
        // Load middleware
        $this->loadMiddleware();
    }
    
    private function setErrorReporting(): void {
        if ($this->config->get('app.debug', false)) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
        }
    }
    
    private function loadRoutes(): void {
        $routesFile = __DIR__ . '/../config/routes.php';
        if (file_exists($routesFile)) {
            require $routesFile;
        }
    }
    
    private function loadMiddleware(): void {
        $middlewareFile = __DIR__ . '/../config/middleware.php';
        if (file_exists($middlewareFile)) {
            $this->middleware = require $middlewareFile;
        }
    }
    
    public function run(): void {
        try {
            $request = Request::capture();
            $response = $this->router->dispatch($request);
            $response->send();
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
    
    private function handleException(\Exception $e): void {
        $logger = $this->container->get(Logger::class);
        $logger->error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        if ($this->config->get('app.debug', false)) {
            echo "<h1>Error</h1>";
            echo "<p>{$e->getMessage()}</p>";
            echo "<pre>{$e->getTraceAsString()}</pre>";
        } else {
            echo "An error occurred. Please try again later.";
        }
    }
    
    public function getContainer(): Container {
        return $this->container;
    }
    
    public function getRouter(): Router {
        return $this->router;
    }
    
    public function getConfig(): Config {
        return $this->config;
    }
}
```

### 3.2 Container.php (Dependency Injection)

```php
<?php
namespace Core;

class Container {
    private array $bindings = [];
    private array $instances = [];
    
    public function bind(string $abstract, callable $concrete): void {
        $this->bindings[$abstract] = $concrete;
    }
    
    public function singleton(string $abstract, callable $concrete): void {
        $this->bindings[$abstract] = function() use ($concrete) {
            if (!isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $concrete();
            }
            return $this->instances[$abstract];
        };
    }
    
    public function get(string $abstract) {
        if (!isset($this->bindings[$abstract])) {
            if (class_exists($abstract)) {
                return $this->resolve($abstract);
            }
            throw new \Exception("No binding found for {$abstract}");
        }
        
        return $this->bindings[$abstract]();
    }
    
    private function resolve(string $class): object {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            return new $class;
        }
        
        $parameters = $constructor->getParameters();
        $dependencies = [];
        
        foreach ($parameters as $parameter) {
            if ($parameter->getType() && !$parameter->getType()->isBuiltin()) {
                $dependencies[] = $this->get($parameter->getType()->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                throw new \Exception("Cannot resolve dependency {$parameter->getName()}");
            }
        }
        
        return $reflection->newInstanceArgs($dependencies);
    }
}
```

### 3.3 Router.php (URL Routing)

```php
<?php
namespace Core;

class Router {
    private Container $container;
    private array $routes = [];
    private array $namedRoutes = [];
    private array $middlewareGroups = [];
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function get(string $path, callable|array $handler): self {
        return $this->addRoute('GET', $path, $handler);
    }
    
    public function post(string $path, callable|array $handler): self {
        return $this->addRoute('POST', $path, $handler);
    }
    
    public function put(string $path, callable|array $handler): self {
        return $this->addRoute('PUT', $path, $handler);
    }
    
    public function delete(string $path, callable|array $handler): self {
        return $this->addRoute('DELETE', $path, $handler);
    }
    
    public function group(array $attributes, callable $callback): self {
        $previousPrefix = $this->getAttribute('prefix', '');
        $previousMiddleware = $this->getAttribute('middleware', []);
        
        if (isset($attributes['prefix'])) {
            $this->setAttribute('prefix', $previousPrefix . $attributes['prefix']);
        }
        
        if (isset($attributes['middleware'])) {
            $currentMiddleware = $this->getAttribute('middleware', []);
            $this->setAttribute('middleware', array_merge($currentMiddleware, (array)$attributes['middleware']));
        }
        
        $callback($this);
        
        $this->setAttribute('prefix', $previousPrefix);
        $this->setAttribute('middleware', $previousMiddleware);
        
        return $this;
    }
    
    public function middleware(string|array $middleware): self {
        $currentMiddleware = $this->getAttribute('middleware', []);
        $this->setAttribute('middleware', array_merge($currentMiddleware, (array)$middleware));
        return $this;
    }
    
    public function name(string $name): self {
        $lastRouteKey = array_key_last($this->routes);
        $this->namedRoutes[$name] = $lastRouteKey;
        return $this;
    }
    
    private function addRoute(string $method, string $path, callable|array $handler): self {
        $prefix = $this->getAttribute('prefix', '');
        $middleware = $this->getAttribute('middleware', []);
        
        $this->routes[] = [
            'method' => $method,
            'path' => $prefix . $path,
            'handler' => $handler,
            'middleware' => $middleware,
            'regex' => $this->convertPathToRegex($prefix . $path)
        ];
        
        return $this;
    }
    
    private function convertPathToRegex(string $path): string {
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    public function dispatch(Request $request): Response {
        $method = $request->getMethod();
        $uri = $request->getUri();
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($route['regex'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $request->setParams($params);
                
                return $this->handleRoute($route, $request);
            }
        }
        
        return $this->notFound();
    }
    
    private function handleRoute(array $route, Request $request): Response {
        // Run middleware
        foreach ($route['middleware'] as $middleware) {
            $middlewareInstance = $this->container->get($middleware);
            if (!$middlewareInstance instanceof MiddlewareInterface) {
                throw new \Exception("{$middleware} must implement MiddlewareInterface");
            }
            
            $response = $middlewareInstance->handle($request, function($req) {
                return null; // Continue to next middleware
            });
            
            if ($response !== null) {
                return $response; // Middleware returned response, stop pipeline
            }
        }
        
        // Execute handler
        $handler = $route['handler'];
        
        if (is_callable($handler)) {
            return $handler($request);
        }
        
        if (is_array($handler)) {
            [$controller, $method] = $handler;
            $controllerInstance = $this->container->get($controller);
            
            if (!method_exists($controllerInstance, $method)) {
                throw new \Exception("Method {$method} not found in {$controller}");
            }
            
            return $controllerInstance->$method($request);
        }
        
        throw new \Exception("Invalid route handler");
    }
    
    private function notFound(): Response {
        return new Response('Not Found', 404);
    }
    
    public function url(string $name, array $params = []): string {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Route {$name} not found");
        }
        
        $route = $this->routes[$this->namedRoutes[$name]];
        $url = $route['path'];
        
        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }
        
        return $url;
    }
    
    private array $attributes = [];
    
    private function getAttribute(string $key, $default = null) {
        return $this->attributes[$key] ?? $default;
    }
    
    private function setAttribute(string $key, $value): void {
        $this->attributes[$key] = $value;
    }
}
```

### 3.4 Database.php (PDO Wrapper)

```php
<?php
namespace Core;

use PDO;
use PDOException;

class Database {
    private ?PDO $connection = null;
    private array $config;
    
    public function __construct(array $config) {
        $this->config = $config;
    }
    
    public function connect(): PDO {
        if ($this->connection === null) {
            try {
                $dsn = sprintf(
                    '%s:host=%s;port=%s;dbname=%s;charset=%s',
                    $this->config['driver'] ?? 'mysql',
                    $this->config['host'] ?? 'localhost',
                    $this->config['port'] ?? '3306',
                    $this->config['database'] ?? '',
                    $this->config['charset'] ?? 'utf8mb4'
                );
                
                $this->connection = new PDO($dsn, $this->config['username'], $this->config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }
        }
        
        return $this->connection;
    }
    
    public function query(string $sql, array $params = []): array {
        try {
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Query failed: " . $e->getMessage());
        }
    }
    
    public function execute(string $sql, array $params = []): bool {
        try {
            $stmt = $this->connect()->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new \Exception("Execute failed: " . $e->getMessage());
        }
    }
    
    public function fetch(string $sql, array $params = []): ?array {
        $result = $this->query($sql, $params);
        return $result[0] ?? null;
    }
    
    public function insert(string $table, array $data): int {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        $this->execute($sql, array_values($data));
        return (int)$this->connect()->lastInsertId();
    }
    
    public function update(string $table, array $data, string $where, array $whereParams = []): bool {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = ?";
        }
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $table,
            implode(', ', $setParts),
            $where
        );
        
        return $this->execute($sql, array_merge(array_values($data), $whereParams));
    }
    
    public function delete(string $table, string $where, array $params = []): bool {
        $sql = sprintf("DELETE FROM %s WHERE %s", $table, $where);
        return $this->execute($sql, $params);
    }
    
    public function transaction(callable $callback): mixed {
        try {
            $this->connect()->beginTransaction();
            $result = $callback();
            $this->connect()->commit();
            return $result;
        } catch (\Exception $e) {
            $this->connect()->rollBack();
            throw $e;
        }
    }
    
    public function getConnection(): PDO {
        return $this->connect();
    }
}
```

### 3.5 Model.php (Base Model)

```php
<?php
namespace Core;

abstract class Model {
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    
    public function __construct() {
        $this->db = app()->getContainer()->get(Database::class);
    }
    
    public function find(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->fetch($sql, [$id]);
    }
    
    public function all(): array {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->query($sql);
    }
    
    public function where(string $column, string $operator, mixed $value): self {
        $this->whereConditions[] = [$column, $operator, $value];
        return $this;
    }
    
    public function get(): array {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($this->whereConditions)) {
            $whereParts = [];
            foreach ($this->whereConditions as $condition) {
                $whereParts[] = "{$condition[0]} {$condition[1]} ?";
                $params[] = $condition[2];
            }
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }
        
        $this->whereConditions = []; // Reset
        return $this->db->query($sql, $params);
    }
    
    public function first(): ?array {
        $results = $this->get();
        return $results[0] ?? null;
    }
    
    public function create(array $data): int {
        $filteredData = $this->filterFillable($data);
        return $this->db->insert($this->table, $filteredData);
    }
    
    public function update(int $id, array $data): bool {
        $filteredData = $this->filterFillable($data);
        return $this->db->update($this->table, $filteredData, "{$this->primaryKey} = ?", [$id]);
    }
    
    public function delete(int $id): bool {
        return $this->db->delete($this->table, "{$this->primaryKey} = ?", [$id]);
    }
    
    public function paginate(int $page = 1, int $perPage = 15): array {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table} LIMIT {$perPage} OFFSET {$offset}";
        $data = $this->db->query($sql);
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $total = $this->db->fetch($countSql)['total'];
        
        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage)
            ]
        ];
    }
    
    private function filterFillable(array $data): array {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    private array $whereConditions = [];
}
```

### 3.6 Controller.php (Base Controller)

```php
<?php
namespace Core;

abstract class Controller {
    protected Request $request;
    protected Response $response;
    protected View $view;
    protected Auth $auth;
    
    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
        $this->view = app()->getContainer()->get(View::class);
        $this->auth = app()->getContainer()->get(Auth::class);
    }
    
    protected function json(array $data, int $status = 200): Response {
        return $this->response->json($data, $status);
    }
    
    protected function view(string $template, array $data = []): string {
        return $this->view->render($template, $data);
    }
    
    protected function redirect(string $url): Response {
        return $this->response->redirect($url);
    }
    
    protected function back(): Response {
        return $this->response->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
    
    protected function validate(Request $request, array $rules): array {
        $validator = app()->getContainer()->get(Validator::class);
        return $validator->validate($request->all(), $rules);
    }
    
    protected function authorize(string $ability, mixed $argument = null): void {
        if (!$this->auth->can($ability, $argument)) {
            abort(403, 'Unauthorized');
        }
    }
}
```

### 3.7 Auth.php (JWT Authentication)

```php
<?php
namespace Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private string $secret;
    private int $expiry = 3600; // 1 hour
    private string $algorithm = 'HS256';
    
    public function __construct() {
        $config = app()->getConfig();
        $this->secret = $config->get('app.jwt_secret', 'your-secret-key');
        $this->expiry = $config->get('app.jwt_expiry', 3600);
    }
    
    public function generateToken(array $payload): string {
        $issuedAt = time();
        $expire = $issuedAt + $this->expiry;
        
        $tokenPayload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire
        ]);
        
        return JWT::encode($tokenPayload, $this->secret, $this->algorithm);
    }
    
    public function validateToken(string $token): ?array {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
            return (array)$decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    public function user(): ?array {
        $session = app()->getContainer()->get(Session::class);
        return $session->get('user');
    }
    
    public function id(): ?int {
        return $this->user()['id'] ?? null;
    }
    
    public function check(): bool {
        return $this->user() !== null;
    }
    
    public function login(array $credentials): bool {
        $userModel = new \App\Models\User();
        $user = $userModel->where('email', '=', $credentials['email'])->first();
        
        if (!$user || !password_verify($credentials['password'], $user['password'])) {
            return false;
        }
        
        $token = $this->generateToken(['user_id' => $user['id']]);
        
        $session = app()->getContainer()->get(Session::class);
        $session->set('user', $user);
        $session->set('token', $token);
        
        return true;
    }
    
    public function logout(): void {
        $session = app()->getContainer()->get(Session::class);
        $session->destroy();
    }
    
    public function can(string $ability, mixed $argument = null): bool {
        $user = $this->user();
        
        if (!$user) {
            return false;
        }
        
        // Superadmin can do everything
        if ($user['role'] === 'superadmin') {
            return true;
        }
        
        // Implement role-based permissions
        $permissions = $this->getRolePermissions($user['role']);
        return in_array($ability, $permissions);
    }
    
    private function getRolePermissions(string $role): array {
        $config = app()->getConfig();
        $roles = $config->get('roles', []);
        
        return $roles[$role]['permissions'] ?? [];
    }
}
```

### 3.8 Middleware Interface & Implementation

```php
<?php
namespace Core;

interface MiddlewareInterface {
    public function handle(Request $request, callable $next): Response;
}
```

**AuthMiddleware.php:**
```php
<?php
namespace App\Middleware;

use Core\MiddlewareInterface;
use Core\Request;
use Core\Response;
use Core\Auth;

class AuthMiddleware implements MiddlewareInterface {
    public function handle(Request $request, callable $next): Response {
        $auth = app()->getContainer()->get(Auth::class);
        
        if (!$auth->check()) {
            if ($request->isAjax()) {
                return new Response(['error' => 'Unauthorized'], 401);
            }
            return new Response()->redirect('/login');
        }
        
        return $next($request);
    }
}
```

**RoleMiddleware.php:**
```php
<?php
namespace App\Middleware;

use Core\MiddlewareInterface;
use Core\Request;
use Core\Response;
use Core\Auth;

class RoleMiddleware implements MiddlewareInterface {
    private array $roles;
    
    public function __construct(array $roles) {
        $this->roles = $roles;
    }
    
    public function handle(Request $request, callable $next): Response {
        $auth = app()->getContainer()->get(Auth::class);
        $user = $auth->user();
        
        if (!$user || !in_array($user['role'], $this->roles)) {
            if ($request->isAjax()) {
                return new Response(['error' => 'Forbidden'], 403);
            }
            return new Response()->redirect('/forbidden');
        }
        
        return $next($request);
    }
}
```

## 4. Configuration Files

### config/app.php
```php
<?php
return [
    'name' => 'MediCore Pharmacy System',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => getenv('APP_DEBUG') === 'true',
    'url' => getenv('APP_URL') ?: 'http://localhost',
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id',
    'jwt_secret' => getenv('JWT_SECRET') ?: 'your-secret-key-change-in-production',
    'jwt_expiry' => 3600,
    'log_path' => __DIR__ . '/../storage/logs',
];
```

### config/database.php
```php
<?php
return [
    'driver' => 'mysql',
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: '3306',
    'database' => getenv('DB_DATABASE') ?: 'medicore',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];
```

### config/routes.php
```php
<?php
use Core\Application;
use Core\Router;

$router = app()->getRouter();

// Auth routes
$router->get('/login', [\App\Controllers\AuthController::class, 'showLogin'])->name('login');
$router->post('/login', [\App\Controllers\AuthController::class, 'login']);
$router->get('/register', [\App\Controllers\AuthController::class, 'showRegister'])->name('register');
$router->post('/register', [\App\Controllers\AuthController::class, 'register']);
$router->post('/logout', [\App\Controllers\AuthController::class, 'logout'])->name('logout');

// Protected routes
$router->group(['middleware' => ['auth']], function($router) {
    // Dashboard
    $router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Products
    $router->get('/products', [\App\Controllers\ProductController::class, 'index'])->name('products.index');
    $router->get('/products/create', [\App\Controllers\ProductController::class, 'create'])->name('products.create');
    $router->post('/products', [\App\Controllers\ProductController::class, 'store'])->name('products.store');
    $router->get('/products/{id}', [\App\Controllers\ProductController::class, 'show'])->name('products.show');
    $router->get('/products/{id}/edit', [\App\Controllers\ProductController::class, 'edit'])->name('products.edit');
    $router->put('/products/{id}', [\App\Controllers\ProductController::class, 'update'])->name('products.update');
    $router->delete('/products/{id}', [\App\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
    
    // Sales
    $router->get('/sales', [\App\Controllers\SaleController::class, 'index'])->name('sales.index');
    $router->get('/sales/create', [\App\Controllers\SaleController::class, 'create'])->name('sales.create');
    $router->post('/sales', [\App\Controllers\SaleController::class, 'store'])->name('sales.store');
    $router->get('/sales/{id}', [\App\Controllers\SaleController::class, 'show'])->name('sales.show');
    
    // Reports
    $router->get('/reports', [\App\Controllers\ReportController::class, 'index'])->name('reports.index');
    $router->get('/reports/sales', [\App\Controllers\ReportController::class, 'sales'])->name('reports.sales');
    $router->get('/reports/inventory', [\App\Controllers\ReportController::class, 'inventory'])->name('reports.inventory');
});
```

### config/middleware.php
```php
<?php
return [
    'auth' => \App\Middleware\AuthMiddleware::class,
    'role' => \App\Middleware\RoleMiddleware::class,
    'csrf' => \App\Middleware\CsrfMiddleware::class,
];
```

## 5. Entry Point

### public/index.php
```php
<?php
// Load autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Initialize application
$app = new Core\Application();

// Run application
$app->run();
```

## 6. composer.json

```json
{
    "name": "medicore/framework",
    "description": "Custom PHP Micro Framework for Pharmacy Management System",
    "type": "project",
    "require": {
        "php": ">=8.1",
        "firebase/php-jwt": "^6.10",
        "vlucas/phpdotenv": "^5.5",
        "monolog/monolog": "^3.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "squizlabs/php_codesniffer": "^3.7"
    },
    "autoload": {
        "psr-4": {
            "Core\\": "core/",
            "App\\": "app/"
        }
    },
    "scripts": {
        "test": "phpunit",
        "cs-check": "phpcs --standard=PSR12",
        "cs-fix": "phpcbf --standard=PSR12"
    }
}
```

---

**Document Status:** Approved  
**Next Phase:** Implementation Roadmap