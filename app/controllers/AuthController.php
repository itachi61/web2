<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class AuthController extends Controller {
    
    // --- ĐĂNG NHẬP ---
    public function login() {
        // Nếu đã đăng nhập rồi thì đá về trang chủ
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $userModel = $this->model('UserModel');
            $user = $userModel->login($email, $password);

            if ($user) {
                // Lưu session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['phone'] = $user['phone'] ?? '';
                $_SESSION['address'] = $user['address'] ?? '';

                // Chuyển hướng dựa trên quyền
                if ($user['role'] == 'admin') {
                    header('Location: ' . BASE_URL . 'admin');
                } else {
                    header('Location: ' . BASE_URL);
                }
            } else {
                $this->view('layouts/header', ['title' => 'Đăng nhập']);
                $this->view('auth/login', ['error' => 'Email hoặc mật khẩu không đúng!']);
                $this->view('layouts/footer');
            }
        } else {
            $this->view('layouts/header', ['title' => 'Đăng nhập']);
            $this->view('auth/login');
            $this->view('layouts/footer');
        }
    }

    // --- ĐĂNG KÝ ---
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            $password = $_POST['password'];
            $confirm = $_POST['confirm_password'];

            if ($password != $confirm) {
                $this->view('layouts/header', ['title' => 'Đăng ký']);
                $this->view('auth/register', ['error' => 'Mật khẩu nhập lại không khớp!']);
                $this->view('layouts/footer');
                return;
            }

            $userModel = $this->model('UserModel');
            
            if ($userModel->register($fullname, $email, $password, $phone, $address)) {
                header('Location: ' . BASE_URL . 'auth/login');
            } else {
                $this->view('layouts/header', ['title' => 'Đăng ký']);
                $this->view('auth/register', ['error' => 'Đăng ký thất bại (Email có thể đã tồn tại)']);
                $this->view('layouts/footer');
            }
        } else {
            $this->view('layouts/header', ['title' => 'Đăng ký']);
            $this->view('auth/register');
            $this->view('layouts/footer');
        }
    }

    // --- ĐĂNG XUẤT ---
    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL);
    }

    // --- XEM THÔNG TIN CÁ NHÂN ---
    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        $userModel = $this->model('UserModel');
        $user = $userModel->getUserById($_SESSION['user_id']);
        
        $this->view('auth/profile', [
            'title' => 'Thông tin cá nhân',
            'user' => $user,
            'success' => $_SESSION['profile_success'] ?? null,
            'error' => $_SESSION['profile_error'] ?? null
        ]);
        unset($_SESSION['profile_success'], $_SESSION['profile_error']);
    }

    // --- CẬP NHẬT THÔNG TIN ---
    public function updateProfile() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . BASE_URL . 'auth/profile');
            exit;
        }

        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $currentPw = $_POST['current_password'] ?? '';
        $newPw = $_POST['new_password'] ?? '';

        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Update name & email
            $stmt = $db->prepare("UPDATE users SET fullname = ?, email = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->execute([$fullname, $email, $phone, $address, $_SESSION['user_id']]);
            $_SESSION['name'] = $fullname;
            $_SESSION['phone'] = $phone;
            $_SESSION['address'] = $address;

            // Change password if provided
            if (!empty($currentPw) && !empty($newPw)) {
                $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($currentPw, $user['password'])) {
                    $hashed = password_hash($newPw, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashed, $_SESSION['user_id']]);
                    $_SESSION['profile_success'] = 'Cập nhật thông tin và mật khẩu thành công!';
                } else {
                    $_SESSION['profile_error'] = 'Mật khẩu hiện tại không đúng!';
                    header('Location: ' . BASE_URL . 'auth/profile');
                    exit;
                }
            } else {
                $_SESSION['profile_success'] = 'Cập nhật thông tin thành công!';
            }
        } catch (Exception $e) {
            $_SESSION['profile_error'] = 'Lỗi: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . 'auth/profile');
        exit;
    }

    // --- ĐƠN HÀNG CỦA TÔI ---
    public function myOrders() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$_SESSION['user_id']]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Lấy items cho từng đơn
            foreach ($orders as &$order) {
                $stmt2 = $db->prepare("SELECT oi.*, p.name as product_name, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                $stmt2->execute([$order['id']]);
                $order['items'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            $orders = [];
        }

        $this->view('auth/my_orders', ['orders' => $orders]);
    }
}
?>