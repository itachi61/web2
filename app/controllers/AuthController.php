<?php
namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller
{
  public function login(): void
  {
    $this->view('auth/login', [
      'title' => 'Đăng nhập - TechSmart'
    ]);
  }

  public function register(): void
  {
    $this->view('auth/register', [
      'title' => 'Đăng ký - TechSmart'
    ]);
  }
}
