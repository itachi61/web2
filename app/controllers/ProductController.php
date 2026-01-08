<?php
namespace App\Controllers;

use App\Core\Controller;

class ProductController extends Controller
{
  public function index(): void
  {
    // Mock product data
    $products = [
      [
        'id' => 1,
        'name' => 'iPhone 15 Pro Max',
        'price' => 29990000,
        'image' => BASE_URL . '/assets/img/logo.png',
        'category' => 'Điện thoại',
        'stock' => 15
      ],
      [
        'id' => 2,
        'name' => 'MacBook Pro M3',
        'price' => 45990000,
        'image' => BASE_URL . '/assets/img/logo.png',
        'category' => 'Laptop',
        'stock' => 8
      ],
      [
        'id' => 3,
        'name' => 'iPad Air',
        'price' => 15990000,
        'image' => BASE_URL . '/assets/img/logo.png',
        'category' => 'Máy tính bảng',
        'stock' => 0
      ],
      [
        'id' => 4,
        'name' => 'AirPods Pro',
        'price' => 6990000,
        'image' => BASE_URL . '/assets/img/logo.png',
        'category' => 'Phụ kiện',
        'stock' => 25
      ],
      [
        'id' => 5,
        'name' => 'Samsung Galaxy S24 Ultra',
        'price' => 27990000,
        'image' => BASE_URL . '/assets/img/logo.png',
        'category' => 'Điện thoại',
        'stock' => 12
      ],
      [
        'id' => 6,
        'name' => 'Dell XPS 15',
        'price' => 35990000,
        'image' => BASE_URL . '/assets/img/logo.png',
        'category' => 'Laptop',
        'stock' => 5
      ]
    ];

    $this->view('product/index', [
      'title' => 'Sản phẩm - TechSmart',
      'products' => $products
    ]);
  }

  public function detail(): void
  {
    $id = $_GET['id'] ?? 1;
    
    // Mock product detail
    $product = [
      'id' => $id,
      'name' => 'iPhone 15 Pro Max',
      'price' => 29990000,
      'image' => BASE_URL . '/assets/img/bd6a9ca0-75ea-40b0-bf7c-3a531350a291.png',
      'category' => 'Điện thoại',
      'description' => 'iPhone 15 Pro Max với chip A17 Pro mạnh mẽ, camera 48MP, màn hình Super Retina XDR 6.7 inch.',
      'specs' => [
        'Màn hình' => '6.7" Super Retina XDR',
        'Chip' => 'A17 Pro',
        'Camera' => '48MP + 12MP + 12MP',
        'RAM' => '8GB',
        'Bộ nhớ' => '256GB',
        'Pin' => '4422 mAh'
      ]
    ];

    $this->view('product/detail', [
      'title' => $product['name'] . ' - TechSmart',
      'product' => $product
    ]);
  }
}
