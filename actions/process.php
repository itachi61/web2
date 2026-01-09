<?php
// FIX LỖI SESSION PERMISSION DENIED TRÊN XAMPP
$sessDir = dirname(__DIR__) . '/sessions';
if (!is_dir($sessDir)) {
    @mkdir($sessDir, 0777, true);
}
session_save_path($sessDir);
session_start();
require '../config/db.php';
require '../includes/functions.php';

if (isset($_GET['action'])) {
    $act = $_GET['action'];

    if ($act == 'logout') {
        session_destroy();
        header("Location: ../index.php");
        exit();
    }

    if ($act == 'add_to_cart' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $res = $conn->query("SELECT stock FROM products WHERE id=$id");
        if ($res->num_rows > 0 && $res->fetch_assoc()['stock'] > 0) {
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            $_SESSION['cart'][$id] = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id] + 1 : 1;
            $_SESSION['message'] = "Đã thêm vào giỏ hàng!";
        } else {
            $_SESSION['message'] = "Sản phẩm tạm hết hàng!";
        }
        header("Location: ../index.php?page=cart");
        exit();
    }

    if ($act == 'remove_cart' && isset($_GET['id'])) {
        unset($_SESSION['cart'][intval($_GET['id'])]);
        header("Location: ../index.php?page=cart");
        exit();
    }

    if ($act == 'delete_product' && isset($_GET['id']) && isAdmin()) {
        $id = intval($_GET['id']);
        $conn->query("DELETE FROM products WHERE id=$id");
        $_SESSION['message'] = "Đã xóa sản phẩm!";
        header("Location: ../index.php?page=admin_products");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['register'])) {
        $user = $_POST['username'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $name = $_POST['fullname'];
        
        $check = $conn->query("SELECT id FROM users WHERE username='$user'");
        if ($check->num_rows > 0) {
            $_SESSION['message'] = "Tên đăng nhập đã tồn tại!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password, fullname) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $user, $pass, $name);
            if ($stmt->execute()) $_SESSION['message'] = "Đăng ký thành công! Hãy đăng nhập.";
            else $_SESSION['message'] = "Lỗi hệ thống.";
        }
        header("Location: ../index.php?page=auth");
        exit();
    }

    if (isset($_POST['login'])) {
        $user = $_POST['username'];
        $pass = $_POST['password'];
        
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($row = $res->fetch_assoc()) {
            if (password_verify($pass, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['fullname'] = $row['fullname'];
                $_SESSION['role'] = $row['role'];
                header("Location: ../index.php");
                exit();
            }
        }
        $_SESSION['message'] = "Sai thông tin đăng nhập!";
        header("Location: ../index.php?page=auth");
        exit();
    }

    if (isset($_POST['checkout']) && isLoggedIn()) {
        if (!empty($_SESSION['cart'])) {
            $user_id = $_SESSION['user_id'];
            $total = 0;
            
            foreach ($_SESSION['cart'] as $pid => $qty) {
                $p = $conn->query("SELECT price, stock FROM products WHERE id = $pid")->fetch_assoc();
                if ($p['stock'] < $qty) {
                    $_SESSION['message'] = "Sản phẩm ID $pid không đủ số lượng!";
                    header("Location: ../index.php?page=cart");
                    exit();
                }
                $total += $p['price'] * $qty;
            }

            $conn->query("INSERT INTO orders (user_id, total_amount) VALUES ($user_id, $total)");
            $oid = $conn->insert_id;

            foreach ($_SESSION['cart'] as $pid => $qty) {
                $p = $conn->query("SELECT price FROM products WHERE id = $pid")->fetch_assoc();
                $price = $p['price'];
                $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($oid, $pid, $qty, $price)");
                $conn->query("UPDATE products SET stock = stock - $qty WHERE id = $pid");
            }

            unset($_SESSION['cart']);
            $_SESSION['message'] = "Đặt hàng thành công!";
            header("Location: ../index.php?page=home");
            exit();
        }
    }

    if (isset($_POST['save_product']) && isAdmin()) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $cat = $_POST['category'];
        $stock = $_POST['stock'];
        $img = $_POST['image'];
        $desc = $_POST['description'];
        $id = $_POST['id'] ?? null;

        if ($id) {
            $stmt = $conn->prepare("UPDATE products SET name=?, price=?, category=?, stock=?, image=?, description=? WHERE id=?");
            $stmt->bind_param("sdsissi", $name, $price, $cat, $stock, $img, $desc, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO products (name, price, category, stock, image, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdsiss", $name, $price, $cat, $stock, $img, $desc);
        }
        $stmt->execute();
        $_SESSION['message'] = "Lưu sản phẩm thành công!";
        header("Location: ../index.php?page=admin_products");
        exit();
    }

    if (isset($_POST['update_order']) && isAdmin()) {
        $oid = $_POST['order_id'];
        $st = $_POST['status'];
        $conn->query("UPDATE orders SET status='$st' WHERE id=$oid");
        $_SESSION['message'] = "Cập nhật đơn hàng #$oid thành công!";
        header("Location: ../index.php?page=admin_orders");
        exit();
    }

    if (isset($_POST['submit_review']) && isLoggedIn()) {
        $pid = intval($_POST['product_id']);
        $uid = $_SESSION['user_id'];
        $rate = intval($_POST['rating']);
        $cmt = htmlspecialchars($_POST['comment']);

        $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $pid, $uid, $rate, $cmt);
        $stmt->execute();
        
        $_SESSION['message'] = "Cảm ơn bạn đã đánh giá!";
        header("Location: ../index.php?page=detail&id=$pid");
        exit();
    }
}
?>