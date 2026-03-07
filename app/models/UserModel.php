<?php
require_once dirname(__DIR__) . '/core/Database.php';

class UserModel extends Database {
    public function register($fullname, $email, $password, $phone = '', $address = '') {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("INSERT INTO users (fullname, email, phone, address, password) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$fullname, $email, $phone, $address, $hash]);
    }

    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Lấy tất cả users
    public function getAllUsers() {
        $stmt = $this->conn->prepare("SELECT id, fullname, email, role, status, created_at FROM users ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy 1 user theo ID
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Toggle trạng thái khóa/mở khóa
    public function toggleUserStatus($id) {
        $user = $this->getUserById($id);
        $currentStatus = $user['status'] ?? 'active';
        $newStatus = ($currentStatus == 'active') ? 'locked' : 'active';
        
        $stmt = $this->conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        return $stmt->execute([$newStatus, $id]);
    }
}
?>