<?php
require_once 'Database.php';
require_once 'Session.php';

class User {
    private $db;
    private $table = 'tbl_users';
    private $database;

    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }

    public  function google_login($email, $name, $picture) {

        try{
            $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE email = ?");

            $stmt->execute([$email]);
        
            if(!$stmt->fetch()) {
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, email, role) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, 'student']);
            }

                $stmt= $this->db->prepare("SELECT id, name, email, role FROM {$this->table} WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                Session::start();
                Session::set('id', $user['id']);
                Session::set('name', $user['name']);
                Session::set('email', $user['email']);
                Session::set('role', $user['role']);
                Session::set('image_path', $picture);
                return true;

        }catch(PDOexception $e){
            error_log("Google login error: " . $e->getMessage());
            return false;
        }

    }


    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email, password, role FROM {$this->table} WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                Session::start();
                Session::set('id', $user['id']);
                Session::set('name', $user['name']);
                Session::set('email', $user['email']);
                Session::set('role', $user['role']);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function register($name, $email, $password, $role = 'student') {
        try {
            // Check if email exists
            $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already registered'];
            }

            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password, $role]);

            return ['success' => true, 'message' => 'Registration successful'];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }

    public function logout() {
        Session::start();
        
        Session::destroy();
        return true;
    }

    public function getUserById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email, role FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllStudents() {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email FROM {$this->table} WHERE role = 'student'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get students error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllUsers(){
        try{
            $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }
}
