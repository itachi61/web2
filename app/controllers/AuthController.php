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
            $password = $_POST['password'];
            $confirm = $_POST['confirm_password'];

            if ($password != $confirm) {
                $this->view('layouts/header', ['title' => 'Đăng ký']);
                $this->view('auth/register', ['error' => 'Mật khẩu nhập lại không khớp!']);
                $this->view('layouts/footer');
                return;
            }

            $userModel = $this->model('UserModel');
            
            // (Nên kiểm tra email đã tồn tại chưa ở đây, tạm thời bỏ qua cho đơn giản)
            
            if ($userModel->register($fullname, $email, $password)) {
                // Đăng ký thành công -> Chuyển sang login
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
}
?>