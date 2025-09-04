<?php
session_start();
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->ensureRememberTokenColumn();
    }
    
    private function ensureRememberTokenColumn() {
        try {

            $query = "SHOW COLUMNS FROM admin_users LIKE 'remember_token'";
            $stmt = $this->conn->query($query);
            
            if ($stmt->rowCount() == 0) {

                $alterQuery = "ALTER TABLE admin_users ADD COLUMN remember_token VARCHAR(64) NULL, ADD COLUMN remember_token_expires DATETIME NULL";
                $this->conn->exec($alterQuery);
            }
        } catch (Exception $e) {

        }
    }
    
    public function login($username, $password) {
        $query = "SELECT id, username, email, password FROM admin_users WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$username, $username]);
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_username'] = $row['username'];
                $_SESSION['admin_email'] = $row['email'];
                return true;
            }
        }
        return false;
    }
    
    public function loginWithToken($token) {
        $query = "SELECT id, username, email FROM admin_users WHERE remember_token = ? AND remember_token_expires > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$token]);
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            $_SESSION['admin_email'] = $row['email'];

            $this->setRememberToken($token);
            return true;
        }
        return false;
    }
    
    public function setRememberToken($token) {
        if (isset($_SESSION['admin_id'])) {
            $expires = date('Y-m-d H:i:s', time() + (86400 * 30));
            $query = "UPDATE admin_users SET remember_token = ?, remember_token_expires = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$token, $expires, $_SESSION['admin_id']]);
        }
    }
    
    public function logout() {
        if (isset($_SESSION['admin_id'])) {

            $query = "UPDATE admin_users SET remember_token = NULL, remember_token_expires = NULL WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$_SESSION['admin_id']]);
        }
        
        session_destroy();

        setcookie('remember_token', '', time() - 3600, '/');
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }
    
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: ../admin/login.php');
            exit();
        }
    }
}
?>
