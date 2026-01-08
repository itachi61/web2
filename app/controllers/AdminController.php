<?php
namespace App\Controllers;

use App\Core\Controller;

class AdminController extends Controller
{
  public function dashboard(): void
  {
    $stats = [
      'total_products' => 156,
      'total_orders' => 89,
      'total_revenue' => 450000000,
      'total_users' => 234
    ];

    $recentOrders = [
      ['id' => 1, 'customer' => 'Nguyễn Văn A', 'total' => 29990000, 'status' => 'Đang xử lý'],
      ['id' => 2, 'customer' => 'Trần Thị B', 'total' => 15990000, 'status' => 'Đã giao'],
      ['id' => 3, 'customer' => 'Lê Văn C', 'total' => 45990000, 'status' => 'Đang giao']
    ];

    $this->view('admin/dashboard', [
      'title' => 'Dashboard - Admin',
      'stats' => $stats,
      'recentOrders' => $recentOrders
    ], 'admin');
  }

  public function products(): void
  {
    $products = [
      ['id' => 1, 'name' => 'iPhone 15 Pro Max', 'price' => 29990000, 'stock' => 45, 'category' => 'Điện thoại'],
      ['id' => 2, 'name' => 'MacBook Pro M3', 'price' => 45990000, 'stock' => 23, 'category' => 'Laptop'],
      ['id' => 3, 'name' => 'iPad Air', 'price' => 15990000, 'stock' => 67, 'category' => 'Máy tính bảng'],
      ['id' => 4, 'name' => 'AirPods Pro', 'price' => 6990000, 'stock' => 120, 'category' => 'Phụ kiện']
    ];

    $this->view('admin/products/index', [
      'title' => 'Quản lý sản phẩm - Admin',
      'products' => $products
    ], 'admin');
  }

  public function createProduct(): void
  {
    $this->view('admin/products/form', [
      'title' => 'Thêm sản phẩm - Admin',
      'product' => null
    ], 'admin');
  }

  public function editProduct(): void
  {
    $id = $_GET['id'] ?? 1;
    
    $product = [
      'id' => $id,
      'name' => 'iPhone 15 Pro Max',
      'price' => 29990000,
      'stock' => 45,
      'category' => 'Điện thoại',
      'description' => 'iPhone 15 Pro Max với chip A17 Pro'
    ];

    $this->view('admin/products/form', [
      'title' => 'Sửa sản phẩm - Admin',
      'product' => $product
    ], 'admin');
  }
}
