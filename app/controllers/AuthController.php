<?php
namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller
{
  private $userModel;

  public function __construct()
  {
    require_once __DIR__ . '/../models/User.php';
    $this->userModel = new \User();
  }

  public function login(): void
  {
    // Check if already logged in
    if (isset($_SESSION['user_id'])) {
      header('Location: ' . BASE_URL);
      exit;
    }

    $this->view('auth/login', [
      'title' => 'Đăng nhập - TechSmart'
    ]);
  }

  public function handleLogin(): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Validate input
    if (empty($username) || empty($password)) {
      $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }

    // Attempt login
    $user = $this->userModel->login($username, $password);

    if ($user) {
      // Login successful
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['full_name'] = $user['full_name'];

      // Set remember me cookie if checked
      if ($remember) {
        setcookie('remember_user', $user['id'], time() + (86400 * 30), '/'); // 30 days
      }

      $_SESSION['success'] = 'Đăng nhập thành công!';

      // Redirect based on role
      if ($user['role'] === 'admin') {
        header('Location: ' . BASE_URL . '/admin/dashboard');
      } else {
        header('Location: ' . BASE_URL);
      }
      exit;
    } else {
      // Login failed
      $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng';
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
  }

  public function register(): void
  {
    // Check if already logged in
    if (isset($_SESSION['user_id'])) {
      header('Location: ' . BASE_URL);
      exit;
    }

    $this->view('auth/register', [
      'title' => 'Đăng ký - TechSmart'
    ]);
  }

  public function handleRegister(): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . BASE_URL . '/auth/register');
      exit;
    }

    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Validate input
    $errors = [];

    if (empty($username)) {
      $errors[] = 'Vui lòng nhập tên đăng nhập';
    } elseif (strlen($username) < 3) {
      $errors[] = 'Tên đăng nhập phải có ít nhất 3 ký tự';
    } elseif ($this->userModel->usernameExists($username)) {
      $errors[] = 'Tên đăng nhập đã tồn tại';
    }

    if (empty($email)) {
      $errors[] = 'Vui lòng nhập email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Email không hợp lệ';
    } elseif ($this->userModel->emailExists($email)) {
      $errors[] = 'Email đã được sử dụng';
    }

    if (empty($password)) {
      $errors[] = 'Vui lòng nhập mật khẩu';
    } elseif (strlen($password) < 6) {
      $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
    }

    if ($password !== $confirmPassword) {
      $errors[] = 'Mật khẩu xác nhận không khớp';
    }

    if (!empty($errors)) {
      $_SESSION['errors'] = $errors;
      $_SESSION['old'] = $_POST;
      header('Location: ' . BASE_URL . '/auth/register');
      exit;
    }

    // Register user
    $userId = $this->userModel->register([
      'username' => $username,
      'email' => $email,
      'password' => $password,
      'full_name' => $fullName,
      'phone' => $phone
    ]);

    if ($userId) {
      $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
      header('Location: ' . BASE_URL . '/auth/login');
    } else {
      $_SESSION['error'] = 'Đăng ký thất bại. Vui lòng thử lại.';
      header('Location: ' . BASE_URL . '/auth/register');
    }
    exit;
  }

  public function logout(): void
  {
    // Clear session
    session_destroy();
    
    // Clear remember me cookie
    setcookie('remember_user', '', time() - 3600, '/');

    // Redirect to home
    header('Location: ' . BASE_URL);
    exit;
  }
}
