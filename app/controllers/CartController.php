<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class CartController extends Controller
{

    public function index()
    {
        // Lấy giỏ hàng từ Session, nếu chưa có thì là mảng rỗng
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        $this->view('layouts/header', ['title' => 'Giỏ hàng']);
        $this->view('cart/index', ['cart' => $cart]);
        $this->view('layouts/footer');
    }

    public function add($id)
    {
        $productModel = $this->model('ProductModel');
        $product = $productModel->getProductById($id);

        // Kiểm tra nếu sản phẩm không tồn tại trong DB thì không làm gì cả
        if (!$product) {
            header('Location: ' . BASE_URL);
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            // QUAN TRỌNG: Đảm bảo có key 'price' ở dòng dưới đây
            $_SESSION['cart'][$id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'], // <--- Kiểm tra kỹ dòng này
                'image' => $product['image'],
                'quantity' => 1
            ];
        }
        header('Location: ' . BASE_URL . 'cart');
    }

    // AJAX add to cart - trả về JSON, không redirect
    public function ajaxAdd($id)
    {
        header('Content-Type: application/json');

        $productModel = $this->model('ProductModel');
        $product = $productModel->getProductById($id);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => 1
            ];
        }

        // Tính tổng số lượng items trong giỏ
        $totalItems = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalItems += $item['quantity'];
        }

        echo json_encode([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng!',
            'cartCount' => count($_SESSION['cart']),
            'totalItems' => $totalItems
        ]);
        exit;
    }

    public function update()
    {
        if (isset($_POST['qty'])) {
            foreach ($_POST['qty'] as $id => $qty) {
                if ($qty <= 0) {
                    unset($_SESSION['cart'][$id]);
                } else {
                    $_SESSION['cart'][$id]['quantity'] = $qty;
                }
            }
        }
        header('Location: ' . BASE_URL . 'cart');
    }

    public function remove($id)
    {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        header('Location: ' . BASE_URL . 'cart');
    }
}
