<?php
require_once dirname(__DIR__) . '/core/Database.php';

class UserModel extends Database {
    
    /**
     * Đăng ký tài khoản mới với đầy đủ thông tin giao hàng
     */
    public function register($fullname, $email, $password, $phone = null, $address = null, $ward = null, $district = null) {
        // Kiểm tra email đã tồn tại chưa
        if ($this->emailExists($email)) {
            return false;
        }
        
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("
            INSERT INTO users (fullname, email, password, phone, address, ward, district) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$fullname, $email, $hash, $phone, $address, $ward, $district]);
    }

    /**
     * Kiểm tra email đã tồn tại chưa
     */
    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    /**
     * Đăng nhập
     */
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? AND (is_locked = 0 OR is_locked IS NULL)");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Lấy thông tin user theo ID
     */
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật thông tin profile
     */
    public function updateProfile($id, $fullname, $phone, $address, $ward, $district) {
        $stmt = $this->conn->prepare("
            UPDATE users 
            SET fullname = ?, phone = ?, address = ?, ward = ?, district = ?
            WHERE id = ?
        ");
        return $stmt->execute([$fullname, $phone, $address, $ward, $district, $id]);
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword($id, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    /**
     * Lấy tất cả users (cho admin)
     */
    public function getAllUsers() {
        $stmt = $this->conn->query("SELECT id, fullname, email, phone, role, is_locked, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Khóa/mở khóa tài khoản
     */
    public function toggleLock($id, $isLocked) {
        $stmt = $this->conn->prepare("UPDATE users SET is_locked = ? WHERE id = ?");
        return $stmt->execute([$isLocked, $id]);
    }

    /**
     * Khởi tạo lại mật khẩu (cho admin)
     */
    public function resetPassword($id, $newPassword = '123456') {
        return $this->changePassword($id, $newPassword);
    }

    /**
     * Cập nhật trạng thái tài khoản (active/locked)
     */
    public function updateStatus($id, $status) {
        $isLocked = ($status === 'locked') ? 1 : 0;
        $stmt = $this->conn->prepare("UPDATE users SET is_locked = ? WHERE id = ?");
        return $stmt->execute([$isLocked, $id]);
    }

    /**
     * Cập nhật vai trò (user/admin)
     */
    public function updateRole($email, $role) {
        $stmt = $this->conn->prepare("UPDATE users SET role = ? WHERE email = ?");
        return $stmt->execute([$role, $email]);
    }
}
?>