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
    // User routes
    $this->routes['GET']['/'] = ['App\\Controllers\\HomeController', 'index'];
    $this->routes['GET']['/products'] = ['App\\Controllers\\ProductController', 'index'];
    $this->routes['GET']['/product'] = ['App\\Controllers\\ProductController', 'detail'];
    $this->routes['GET']['/cart'] = ['App\\Controllers\\CartController', 'index'];
    $this->routes['GET']['/login'] = ['App\\Controllers\\AuthController', 'login'];
    $this->routes['GET']['/register'] = ['App\\Controllers\\AuthController', 'register'];
    
    // Admin routes
    $this->routes['GET']['/admin'] = ['App\\Controllers\\AdminController', 'dashboard'];
    $this->routes['GET']['/admin/products'] = ['App\\Controllers\\AdminController', 'products'];
    $this->routes['GET']['/admin/products/create'] = ['App\\Controllers\\AdminController', 'createProduct'];
    $this->routes['GET']['/admin/products/edit'] = ['App\\Controllers\\AdminController', 'editProduct'];
  }

  public function run(): void
  {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    
    // Remove base path
    $base = BASE_URL;
    if (str_starts_with($uri, $base)) {
      $uri = substr($uri, strlen($base));
      if ($uri === '') $uri = '/';
    }

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $handler = $this->routes[$method][$uri] ?? null;

    if (!$handler) {
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
