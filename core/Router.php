<?php

namespace Core;

/**
 * Router Class
 * Handles URL routing and request dispatching
 */

class Router
{
    private Container $container;
    private array $routes = [];
    private array $namedRoutes = [];
    private array $middlewareGroups = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Register a GET route
     */
    public function get(string $path, callable|array $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    /**
     * Register a POST route
     */
    public function post(string $path, callable|array $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    /**
     * Register a PUT route
     */
    public function put(string $path, callable|array $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    /**
     * Register a DELETE route
     */
    public function delete(string $path, callable|array $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Register a PATCH route
     */
    public function patch(string $path, callable|array $handler): self
    {
        return $this->addRoute('PATCH', $path, $handler);
    }

    /**
     * Create a route group with shared attributes
     */
    public function group(array $attributes, callable $callback): self
    {
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

    /**
     * Add middleware to the current route
     */
    public function middleware(string|array $middleware): self
    {
        if (empty($this->routes)) {
            // Apply to group level
            $currentMiddleware = $this->getAttribute('middleware', []);
            $this->setAttribute('middleware', array_merge($currentMiddleware, (array)$middleware));
        } else {
            // Apply to last added route
            $lastRouteKey = array_key_last($this->routes);
            $currentMiddleware = $this->routes[$lastRouteKey]['middleware'] ?? [];
            $this->routes[$lastRouteKey]['middleware'] = array_merge($currentMiddleware, (array)$middleware);
        }
        return $this;
    }

    /**
     * Name a route for URL generation
     */
    public function name(string $name): self
    {
        $lastRouteKey = array_key_last($this->routes);
        $this->namedRoutes[$name] = $lastRouteKey;
        return $this;
    }

    /**
     * Add a route to the routes array
     */
    private function addRoute(string $method, string $path, callable|array $handler): self
    {
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

    /**
     * Convert path to regex pattern
     */
    private function convertPathToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Dispatch a request to the appropriate handler
     */
    public function dispatch(Request $request): Response
    {
        // Ensure container is set in request
        $request->setContainer($this->container);

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

    /**
     * Handle a matched route
     */
    private function handleRoute(array $route, Request $request): Response
    {
        // Ensure container is set (before middleware)
        if ($request->getContainer() === null) {
            $request->setContainer($this->container);
        }

        // Run middleware
        foreach ($route['middleware'] as $middleware) {
            // Resolve middleware from container or instantiate directly
            if (is_string($middleware) && class_exists($middleware)) {
                $middlewareInstance = $this->container->get($middleware);
            } elseif (is_object($middleware)) {
                $middlewareInstance = $middleware;
            } else {
                // Skip if middleware is just a string name
                continue;
            }

            if (!is_object($middlewareInstance) || !$middlewareInstance instanceof MiddlewareInterface) {
                throw new \Exception("Middleware must implement MiddlewareInterface");
            }

            $response = $middlewareInstance->handle($request, function ($req) {
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
            
            // Set container on request before resolving controller
            $request->setContainer($this->container);
            
            // Create controller instance with Request and Container
            $controllerInstance = new $controller($request, $this->container);

            if (!method_exists($controllerInstance, $method)) {
                throw new \Exception("Method {$method} not found in {$controller}");
            }

            // Call the method with request
            return $controllerInstance->$method($request);
        }

        throw new \Exception("Invalid route handler");
    }

    /**
     * Return 404 Not Found response
     */
    private function notFound(): Response
    {
        $errorView = __DIR__ . '/../app/Views/errors/404.php';
        if (file_exists($errorView)) {
            ob_start();
            include $errorView;
            $html = ob_get_clean();
            return new Response($html, 404);
        }
        return new Response('404 Not Found', 404);
    }

    /**
     * Generate URL from route name
     */
    public function url(string $name, array $params = []): string
    {
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

    /**
     * Get attribute for route building
     */
    private array $attributes = [];

    private function getAttribute(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    private function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }
}