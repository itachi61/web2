<?php
require_once dirname(__DIR__) . '/core/Database.php';

class UserModel extends Database {
    public function register($fullname, $email, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$fullname, $email, $hash]);
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
}
?>