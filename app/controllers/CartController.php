<?php
namespace App\Controllers;

use App\Core\Controller;

class CartController extends Controller
{
  public function index(): void
  {
    // Mock cart items
    $cartItems = [
      [
        'id' => 1,
        'product_name' => 'iPhone 15 Pro Max',
        'price' => 29990000,
        'quantity' => 1,
        'image' => BASE_URL . '/assets/img/bd6a9ca0-75ea-40b0-bf7c-3a531350a291.png'
      ],
      [
        'id' => 2,
        'product_name' => 'AirPods Pro',
        'price' => 6990000,
        'quantity' => 2,
        'image' => BASE_URL . '/assets/img/bd6a9ca0-75ea-40b0-bf7c-3a531350a291.png'
      ]
    ];

    $total = array_reduce($cartItems, function($sum, $item) {
      return $sum + ($item['price'] * $item['quantity']);
    }, 0);

    $this->view('cart/index', [
      'title' => 'Giỏ hàng - TechSmart',
      'cartItems' => $cartItems,
      'total' => $total
    ]);
  }
}
