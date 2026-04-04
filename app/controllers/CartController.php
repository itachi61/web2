<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class CartController extends Controller
{

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['login_message'] = 'Vui lòng đăng nhập trước khi mua hàng!';
            $_SESSION['redirect_after_login'] = BASE_URL . 'cart';
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        // Load stock data for each cart item
        $productModel = $this->model('ProductModel');
        $stockMap = [];
        foreach ($cart as $id => $item) {
            $p = $productModel->getProductById($id);
            $stockMap[$id] = $p ? intval($p['stock']) : 0;
            // Auto-cap quantity to stock
            if ($cart[$id]['quantity'] > $stockMap[$id]) {
                $_SESSION['cart'][$id]['quantity'] = max($stockMap[$id], 0);
                $cart[$id]['quantity'] = $stockMap[$id];
            }
            // Remove out-of-stock items
            if ($stockMap[$id] <= 0) {
                unset($_SESSION['cart'][$id]);
                unset($cart[$id]);
            }
        }

        $this->view('layouts/header', ['title' => 'Giỏ hàng']);
        $this->view('cart/index', ['cart' => $cart, 'stockMap' => $stockMap]);
        $this->view('layouts/footer');
    }

    public function add($id)
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['login_message'] = 'Vui lòng đăng nhập trước khi mua hàng!';
            $_SESSION['redirect_after_login'] = BASE_URL . 'cart';
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        $productModel = $this->model('ProductModel');
        $product = $productModel->getProductById($id);

        // Kiểm tra nếu sản phẩm không tồn tại trong DB thì không làm gì cả
        if (!$product) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Chặn mua SP hết hàng
        if (($product['stock'] ?? 0) <= 0) {
            $_SESSION['cart_error'] = 'Sản phẩm "' . $product['name'] . '" hiện đã hết hàng!';
            header('Location: ' . BASE_URL);
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $currentQty = $_SESSION['cart'][$id]['quantity'];
            if ($currentQty >= $product['stock']) {
                $_SESSION['cart_error'] = 'Số lượng "' . $product['name'] . '" đã đạt tối đa tồn kho (' . $product['stock'] . ')';
                header('Location: ' . BASE_URL . 'cart');
                exit;
            }
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
        header('Location: ' . BASE_URL . 'cart');
    }

    // AJAX add to cart - trả về JSON, không redirect
    public function ajaxAdd($id)
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['login_message'] = 'Vui lòng đăng nhập trước khi mua hàng!';
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thêm giỏ hàng!', 'needLogin' => true]);
            exit;
        }

        $productModel = $this->model('ProductModel');
        $product = $productModel->getProductById($id);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
            exit;
        }

        // Chặn mua SP hết hàng
        if (($product['stock'] ?? 0) <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm "' . $product['name'] . '" hiện đã hết hàng!']);
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $currentQty = $_SESSION['cart'][$id]['quantity'];
            if ($currentQty >= $product['stock']) {
                echo json_encode(['success' => false, 'message' => 'Đã đạt tối đa tồn kho (' . $product['stock'] . ')']);
                exit;
            }
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
            $productModel = $this->model('ProductModel');
            foreach ($_POST['qty'] as $id => $qty) {
                if ($qty <= 0) {
                    unset($_SESSION['cart'][$id]);
                } else {
                    $p = $productModel->getProductById($id);
                    $maxStock = $p ? intval($p['stock']) : 0;
                    if ($qty > $maxStock) {
                        $qty = $maxStock;
                        $_SESSION['cart_error'] = 'Số lượng đã được điều chỉnh về tối đa tồn kho.';
                    }
                    if ($qty <= 0) {
                        unset($_SESSION['cart'][$id]);
                    } else {
                        $_SESSION['cart'][$id]['quantity'] = $qty;
                    }
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
