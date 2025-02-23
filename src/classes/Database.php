<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'new_project';
    private $username = 'root';
    private $password = '';
    private $pdo;
    private static $instance;
   

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname}", 
                                $this->username, 
                                $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    public function getConnection() {
        return $this->pdo;
    }

    public static function getInstance() {
        if(self::$instance){
            self::$instance = new Database();
        }
        return self::$instance;
    }
}
