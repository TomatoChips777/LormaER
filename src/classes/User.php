<?php
require_once 'Database.php';
require_once 'Session.php';

class User
{
    private $db;
    private $table = 'tbl_users';
    private $database;

    public function __construct()
    {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }

    public  function google_login($email, $name, $picture)
    {

        try {
            $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE email = ?");

            $stmt->execute([$email]);

            if (!$stmt->fetch()) {
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, email, role) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, 'student']);
            }

            $stmt = $this->db->prepare("SELECT id, name, email, role FROM {$this->table} WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            Session::start();
            Session::set('id', $user['id']);
            Session::set('name', $user['name']);
            Session::set('email', $user['email']);
            Session::set('role', $user['role']);
            Session::set('image_path', $picture);
            return true;
        } catch (PDOexception $e) {
            error_log("Google login error: " . $e->getMessage());
            return false;
        }
    }


    public function login($email, $password)
    {
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

    public function register($name, $email, $password, $role = 'student')
    {
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

    public function logout()
    {
        Session::start();

        Session::destroy();
        return true;
    }

    public function getUserById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email, role FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllStudents()
    {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email FROM {$this->table} WHERE role = 'student'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get students error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllUsers()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }
    public function getPaginatedUsers($role = 'all', $search = '', $limit = 10, $offset = 0)
    {
        try {
            $params = [];
            $whereClause = [];

            if ($role !== 'all') {
                $whereClause[] = "u.role = ?";
                $params[] = $role;
            }

            if (!empty($search)) {
                $whereClause[] = "(u.name LIKE ? OR u.email LIKE ? OR u.id LIKE ?)";
                $searchTerm = "%$search%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
            }

            $whereStr = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";

            // Cast limit and offset to integers
            $limit = (int)$limit;
            $offset = (int)$offset;

            $query = "
            SELECT u.id, u.name, u.email, u.role, u.created_at, 
                   COUNT(r.id) AS report_count
            FROM {$this->table} u
            LEFT JOIN tbl_reports r ON u.id = r.user_id
            $whereStr
            GROUP BY u.id
            ORDER BY u.created_at DESC
            LIMIT $limit OFFSET $offset
        ";

            error_log("Final SQL Query: " . $query);
            error_log("Parameters: " . print_r($params, true));

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Query result count: " . count($result));

            if (count($result) === 0) {
                // Execute a test query to verify data exists
                $testQuery = "SELECT COUNT(*) as total FROM {$this->table} u";
                $testStmt = $this->db->prepare($testQuery);
                $testStmt->execute();
                $testResult = $testStmt->fetch(PDO::FETCH_ASSOC);
                error_log("Test query total records: " . $testResult['total']);

                error_log("No results found. Last SQL error: " . print_r($stmt->errorInfo(), true));
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Get paginated users error: " . $e->getMessage());
            error_log("SQL State: " . $e->errorInfo[0]);
            error_log("Error Code: " . $e->errorInfo[1]);
            error_log("Error Message: " . $e->errorInfo[2]);
            return [];
        }
    }


    public function getTotalUsers($role, $search)
    {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table}";
            $params = [];

            if ($role !== 'all' || !empty($search)) {
                $query .= " WHERE";
            }

            if ($role !== 'all') {
                $query .= " role = ?";
                $params[] = $role;
            }

            if (!empty($search)) {
                $query .= ($role !== 'all' ? " AND" : "") . " (name LIKE ? OR email LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Get total users error: " . $e->getMessage());
            return false;
        }
    }

    public function getUserRoleCount()
    {
        try {
            $query = "
                SELECT 
                CASE 
                    WHEN role IS NULL OR role = '' THEN 'other' 
                    ELSE role 
                END AS role,
                COUNT(*) as count
                FROM {$this->table}
                GROUP BY role
                ORDER BY role;

            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get report status count error: " . $e->getMessage());
            return [];
        }
    }


    public function updateUserRole($reportId, $role)
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET role = ? WHERE id = ?");
            $result = $stmt->execute([$role, $reportId]);

            return [
                'success' => true,
                'message' => $result ? "User role updated successfully" : "Failed to update user's role"
            ];
        } catch (PDOException $e) {
            error_log("Update user role error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update role'
            ];
        }
    }
}
