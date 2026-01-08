<?php
namespace App\Core;

class App
{
  private array $routes = [];

  public function __construct()
  {
    $this->registerRoutes();
  }

  private function registerRoutes(): void
  {
    // Home routes
    $this->routes['GET']['/'] = ['App\\Controllers\\HomeController', 'index'];
    
    // Product routes
    $this->routes['GET']['/products'] = ['App\\Controllers\\ProductController', 'index'];
    $this->routes['GET']['/product'] = ['App\\Controllers\\ProductController', 'detail'];
    
    // Auth routes
    $this->routes['GET']['/login'] = ['App\\Controllers\\AuthController', 'login'];
    $this->routes['POST']['/login'] = ['App\\Controllers\\AuthController', 'handleLogin'];
    $this->routes['GET']['/register'] = ['App\\Controllers\\AuthController', 'register'];
    $this->routes['POST']['/register'] = ['App\\Controllers\\AuthController', 'handleRegister'];
    $this->routes['GET']['/logout'] = ['App\\Controllers\\AuthController', 'logout'];
    $this->routes['POST']['/logout'] = ['App\\Controllers\\AuthController', 'logout'];
    
    // Cart routes
    $this->routes['GET']['/cart'] = ['App\\Controllers\\CartController', 'index'];
    $this->routes['POST']['/cart/add'] = ['App\\Controllers\\CartController', 'add'];
    $this->routes['POST']['/cart/update'] = ['App\\Controllers\\CartController', 'update'];
    $this->routes['POST']['/cart/remove'] = ['App\\Controllers\\CartController', 'remove'];
    $this->routes['POST']['/cart/clear'] = ['App\\Controllers\\CartController', 'clear'];
    
    // Admin routes
    $this->routes['GET']['/admin'] = ['App\\Controllers\\AdminController', 'dashboard'];
    $this->routes['GET']['/admin/dashboard'] = ['App\\Controllers\\AdminController', 'dashboard'];
    
    // Admin - Products
    $this->routes['GET']['/admin/products'] = ['App\\Controllers\\AdminController', 'products'];
    $this->routes['GET']['/admin/products/create'] = ['App\\Controllers\\AdminController', 'createProduct'];
    $this->routes['POST']['/admin/products/create'] = ['App\\Controllers\\AdminController', 'handleCreateProduct'];
    $this->routes['GET']['/admin/products/edit'] = ['App\\Controllers\\AdminController', 'editProduct'];
    $this->routes['POST']['/admin/products/edit'] = ['App\\Controllers\\AdminController', 'handleEditProduct'];
    $this->routes['POST']['/admin/products/delete'] = ['App\\Controllers\\AdminController', 'deleteProduct'];
    
    // Admin - Orders
    $this->routes['GET']['/admin/orders'] = ['App\\Controllers\\AdminController', 'orders'];
    $this->routes['GET']['/admin/orders/detail'] = ['App\\Controllers\\AdminController', 'orderDetail'];
    $this->routes['POST']['/admin/orders/update-status'] = ['App\\Controllers\\AdminController', 'updateOrderStatus'];
  }

  public function run(): void
  {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    
    // Remove base path if exists
    $base = '/techsmart/public';
    if (str_starts_with($uri, $base)) {
      $uri = substr($uri, strlen($base));
      if ($uri === '' || $uri === false) {
        $uri = '/';
      }
    }

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $handler = $this->routes[$method][$uri] ?? null;

    if (!$handler) {
      // Debug info in development
      if (APP_ENV === 'development') {
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>Requested URI: <strong>$uri</strong></p>";
        echo "<p>Method: <strong>$method</strong></p>";
        echo "<p>Available routes for $method:</p>";
        echo "<pre>";
        print_r($this->routes[$method] ?? []);
        echo "</pre>";
        return;
      }
      
      http_response_code(404);
      echo "404 - Page Not Found";
      return;
    }

    [$class, $action] = $handler;
    
    if (!class_exists($class)) {
      http_response_code(500);
      echo "500 - Controller not found: $class";
      return;
    }

    $controller = new $class();
    $controller->$action();
  }
}
