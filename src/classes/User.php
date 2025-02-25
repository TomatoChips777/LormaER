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
        } catch (PDOException $e) {
            throw new Exception("Google login error");
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
            throw new Exception("Login error");
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
            throw new Exception("Registration error");
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
            throw new Exception("Get user error");
        }
    }

    public function getAllStudents()
    {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email FROM {$this->table} WHERE role = 'student'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Get students error");
        }
    }

    public function getAllUsers()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Get user error");
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

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) === 0) {
                // Execute a test query to verify data exists
                $testQuery = "SELECT COUNT(*) as total FROM {$this->table} u";
                $testStmt = $this->db->prepare($testQuery);
                $testStmt->execute();
                $testResult = $testStmt->fetch(PDO::FETCH_ASSOC);

                if ($testResult['total'] === 0) {
                    return [];
                }
            }

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Get paginated users error");
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
            throw new Exception("Get total users error");
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
            throw new Exception("Get report status count error");
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
            throw new Exception("Update user role error");
        }
    }
}
