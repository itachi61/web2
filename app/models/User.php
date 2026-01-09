<?php

require_once __DIR__ . '/Model.php';

class User extends Model {
    protected $table = 'users';
    
    /**
     * Register a new user
     */
    public function register($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Set default role
        if (!isset($data['role'])) {
            $data['role'] = 'user';
        }
        
        return $this->create($data);
    }
    
    /**
     * Login user
     */
    public function login($username, $password) {
        $user = $this->findWhere('username', $username);
        
        if (!$user) {
            // Try with email
            $user = $this->findWhere('email', $username);
        }
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get user by email
     */
    public function getByEmail($email) {
        return $this->findWhere('email', $email);
    }
    
    /**
     * Get user by username
     */
    public function getByUsername($username) {
        return $this->findWhere('username', $username);
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email) {
        return $this->getByEmail($email) !== false;
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username) {
        return $this->getByUsername($username) !== false;
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        // Remove sensitive fields
        unset($data['password']);
        unset($data['role']);
        
        return $this->update($userId, $data);
    }
    
    /**
     * Change password
     */
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($userId, $password) {
        $user = $this->find($userId);
        if ($user) {
            return password_verify($password, $user['password']);
        }
        return false;
    }
    
    /**
     * Get all users (admin only)
     */
    public function getAllUsers($role = null) {
        if ($role) {
            return $this->where('role', $role);
        }
        return $this->findAll('created_at DESC');
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin($userId) {
        $user = $this->find($userId);
        return $user && $user['role'] === 'admin';
    }
}
