<?php
namespace App\Controllers;

use App\Core\Controller;

class ProductController extends Controller
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
    $categoryId = $_GET['category'] ?? null;
    $search = $_GET['search'] ?? null;

    // Get products based on filters
    if ($search) {
      $products = $this->productModel->search($search);
    } elseif ($categoryId) {
      $products = $this->productModel->getByCategory($categoryId);
    } else {
      $products = $this->productModel->getAllWithCategory();
    }

    // Get all categories for filter
    $categories = $this->categoryModel->getWithProductCount();

    $this->view('product/index', [
      'title' => 'Sản phẩm - TechSmart',
      'products' => $products,
      'categories' => $categories,
      'currentCategory' => $categoryId,
      'searchKeyword' => $search
    ]);
  }

  public function detail(): void
  {
    $slug = $_GET['slug'] ?? null;
    $id = $_GET['id'] ?? null;

    // Get product by slug or id
    if ($slug) {
      $product = $this->productModel->getBySlug($slug);
    } elseif ($id) {
      $product = $this->productModel->find($id);
      if ($product) {
        // Get category info
        $category = $this->categoryModel->find($product['category_id']);
        $product['category_name'] = $category['name'] ?? '';
      }
    } else {
      // Redirect to products page if no slug or id
      header('Location: ' . BASE_URL . '/product');
      exit;
    }

    if (!$product) {
      // Product not found, redirect to products page
      header('Location: ' . BASE_URL . '/product');
      exit;
    }

    // Get related products from same category
    $relatedProducts = $this->productModel->getByCategory($product['category_id'], 4);
    
    // Remove current product from related products
    $relatedProducts = array_filter($relatedProducts, function($p) use ($product) {
      return $p['id'] != $product['id'];
    });

    // Parse specifications if stored as JSON
    if (!empty($product['specifications']) && is_string($product['specifications'])) {
      $product['specs'] = json_decode($product['specifications'], true) ?? [];
    } else {
      $product['specs'] = [];
    }

    // Parse images if stored as JSON
    if (!empty($product['images']) && is_string($product['images'])) {
      $product['image_list'] = json_decode($product['images'], true) ?? [];
    } else {
      $product['image_list'] = [];
    }

    $this->view('product/detail', [
      'title' => $product['name'] . ' - TechSmart',
      'product' => $product,
      'relatedProducts' => $relatedProducts
    ]);
  }
}
