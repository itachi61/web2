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
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validate
            if (empty($email) || empty($password)) {
                $this->view('layouts/header', ['title' => 'Đăng nhập']);
                $this->view('auth/login', ['error' => 'Vui lòng nhập đầy đủ thông tin!']);
                $this->view('layouts/footer');
                return;
            }

            $userModel = $this->model('UserModel');
            $user = $userModel->login($email, $password);

            if ($user) {
                // Lưu session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['phone'] = $user['phone'] ?? '';
                $_SESSION['address'] = $user['address'] ?? '';
                $_SESSION['ward'] = $user['ward'] ?? '';
                $_SESSION['district'] = $user['district'] ?? '';

                // Chuyển hướng dựa trên quyền
                if ($user['role'] == 'admin') {
                    header('Location: ' . BASE_URL . 'admin');
                } else {
                    header('Location: ' . BASE_URL);
                }
                exit;
            } else {
                $this->view('layouts/header', ['title' => 'Đăng nhập']);
                $this->view('auth/login', ['error' => 'Email hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa!']);
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
        // Nếu đã đăng nhập thì về trang chủ
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lấy dữ liệu từ form
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $ward = trim($_POST['ward'] ?? '');
            $district = trim($_POST['district'] ?? '');

            // Lưu lại dữ liệu cũ để hiển thị khi lỗi
            $oldData = [
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'ward' => $ward,
                'district' => $district
            ];

            // Validate required fields
            if (empty($fullname) || empty($email) || empty($password) || empty($phone) || empty($address) || empty($ward) || empty($district)) {
                $this->view('layouts/header', ['title' => 'Đăng ký']);
                $this->view('auth/register', [
                    'error' => 'Vui lòng nhập đầy đủ thông tin bắt buộc!',
                    'old' => $oldData
                ]);
                $this->view('layouts/footer');
                return;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->view('layouts/header', ['title' => 'Đăng ký']);
                $this->view('auth/register', [
                    'error' => 'Email không hợp lệ!',
                    'old' => $oldData
                ]);
                $this->view('layouts/footer');
                return;
            }

            // Validate phone format (10-11 digits)
            if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
                $this->view('layouts/header', ['title' => 'Đăng ký']);
                $this->view('auth/register', [
                    'error' => 'Số điện thoại không hợp lệ (phải có 10-11 số)!',
                    'old' => $oldData
                ]);
                $this->view('layouts/footer');
                return;
            }

            // Validate password length
            if (strlen($password) < 6) {
                $this->view('layouts/header', ['title' => 'Đăng ký']);
                $this->view('auth/register', [
                    'error' => 'Mật khẩu phải có ít nhất 6 ký tự!',
                    'old' => $oldData
                ]);
                $this->view('layouts/footer');
                return;
            }

            // Validate password match
            if ($password !== $confirm) {
                $this->view('layouts/header', ['title' => 'Đăng ký']);
                $this->view('auth/register', [
                    'error' => 'Mật khẩu nhập lại không khớp!',
                    'old' => $oldData
                ]);
                $this->view('layouts/footer');
                return;
            }

            $userModel = $this->model('UserModel');
            
            // Kiểm tra email đã tồn tại
            if ($userModel->emailExists($email)) {
                $this->view('layouts/header', ['title' => 'Đăng ký']);
                $this->view('auth/register', [
                    'error' => 'Email này đã được đăng ký!',
                    'old' => $oldData
                ]);
                $this->view('layouts/footer');
                return;
            }
            
            // Đăng ký với đầy đủ thông tin
            if ($userModel->register($fullname, $email, $password, $phone, $address, $ward, $district)) {
                // Đăng ký thành công -> Chuyển sang login với thông báo
                $_SESSION['register_success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                header('Location: ' . BASE_URL . 'auth/login');
                exit;
            } else {
                $this->view('layouts/header', ['title' => 'Đăng ký']);
                $this->view('auth/register', [
                    'error' => 'Đăng ký thất bại! Vui lòng thử lại.',
                    'old' => $oldData
                ]);
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
        exit;
    }
}
?>