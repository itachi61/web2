<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
  private $productModel;
  private $categoryModel;

  public function __construct()
  {
    require_once __DIR__ . '/../models/Product.php';
    require_once __DIR__ . '/../models/Category.php';
    
    $this->productModel = new \Product();
    $this->categoryModel = new \Category();
  }

  public function index(): void
  {
    // Get featured products (products with sale price)
    $featuredProducts = $this->productModel->getFeatured(8);
    
    // Get latest products
    $latestProducts = $this->productModel->getLatest(8);
    
    // Get categories with product count
    $categories = $this->categoryModel->getWithProductCount();

    $this->view('home/index', [
      'title' => 'Trang chủ - TechSmart',
      'featuredProducts' => $featuredProducts,
      'latestProducts' => $latestProducts,
      'categories' => $categories
    ]);
  }
}
