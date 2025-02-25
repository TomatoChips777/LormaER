<?php
require_once 'Database.php';
require_once 'Session.php';
require_once 'Notification.php';

class Report
{
    private $database;
    private $db;
    private $table = "tbl_reports";

    public function __construct()
    {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }

    public function createReport($location, $issueType, $description, $imagePath)
    {
        try {
            $userId = Session::get('id');
            $userName = Session::get('name');

            $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, location, issue_type, description, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $location, $issueType, $description, $imagePath]);

            $reportId = $this->db->lastInsertId();

            $notification = new Notification();
            $message = "New report submitted by {$userName} - {$issueType} issue at {$location}";
            $notification->createNotification($userId, $reportId, $message);

            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
            $stmt->execute([$reportId]);
            $newReport = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'message' => 'Report submitted successfully',
                'report' => $newReport
            ];
        } catch (PDOException $e) {
            throw new Exception("Report creation error");
        }
    }

    public function getReportsByUser($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to get reports");
        }
    }

    public function getAllReports()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*, u.name as reporter_name 
                FROM {$this->table} r 
                JOIN tbl_users u ON r.user_id = u.id 
                ORDER BY r.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to get all reports");
        }
    }

    public function getIssueTypes($filter)
    {
        try {
            $dateCondition = "";

            if ($filter === 'current_week') {
                $dateCondition = "AND YEARWEEK(created_at, 1) = YEARWEEK(NOW(), 1)";
            } elseif ($filter === 'last_week') {
                $dateCondition = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND created_at < CURDATE()";
            } elseif ($filter === 'last_month') {
                $dateCondition = "AND YEAR(created_at) = YEAR(NOW() - INTERVAL 1 MONTH) 
                                  AND MONTH(created_at) = MONTH(NOW() - INTERVAL 1 MONTH)";
            } elseif ($filter === 'last_year') {
                $dateCondition = "AND YEAR(created_at) = YEAR(NOW()) - 1";
            } elseif ($filter === 'current_month') {
                $dateCondition = "AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";
            } elseif ($filter === 'current_year') {
                $dateCondition = "AND YEAR(created_at) = YEAR(NOW())";
            }

            $query = "SELECT issue_type, COUNT(*) as count 
                      FROM {$this->table} 
                      WHERE 1 $dateCondition 
                      GROUP BY issue_type 
                      ORDER BY count ASC";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to get issue types");
        }
    }

    public function updateStatus($reportId, $status)
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
            $result = $stmt->execute([$status, $reportId]);

            return [
                'success' => true,
                'message' => $result ? 'Status updated successfully' : 'Failed to update status'
            ];
        } catch (PDOException $e) {
            throw new Exception("Failed to update status");
        }
    }

    public function getReportById($reportId)
    {
        try {
            $stmt = $this->db->prepare("SELECT r.*, u.name as user_name 
                                      FROM {$this->table} r 
                                      JOIN tbl_users u ON r.user_id = u.id 
                                      WHERE r.id = ?");
            $stmt->execute([$reportId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to get report");
        }
    }

    public function updateReport($reportId, $location, $issueType, $description, $imagePath, $user_id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET location = ?, issue_type = ?, description = ?, image_path = ? WHERE id = ? and user_id = ?");
            $stmt->execute([$location, $issueType, $description, $imagePath, $reportId, $user_id]);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Failed to update report");
        }
    }

    public function deleteReport($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id=?");
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Failed to delete report");
        }
    }

    public function getReportStatusByType($issueType = null)
    {
        try {
            if (!empty($issueType) && $issueType !== 'all') {
                $query = "SELECT issue_type, status, COUNT(*) as count 
                      FROM {$this->table} 
                      WHERE issue_type = ? 
                      GROUP BY issue_type, status 
                      ORDER BY issue_type, status";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$issueType]);
            } else {
                $query = "SELECT status, COUNT(*) as count 
                      FROM {$this->table} 
                      GROUP BY status 
                      ORDER BY status";
                $stmt = $this->db->prepare($query);
                $stmt->execute();
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to get report status by type");
        }
    }

    public function getPaginatedReports($status = 'all', $search = '', $limit = 10, $offset = 0)
    {
        try {
            $params = [];
            $whereClause = [];

            if ($status !== 'all') {
                $whereClause[] = "r.status = ?";
                $params[] = $status;
            }

            if (!empty($search)) {
                $whereClause[] = "(u.name LIKE ? OR r.location LIKE ? OR r.issue_type LIKE ? OR r.description LIKE ?)";
                $searchTerm = "%$search%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            $whereStr = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";

            $limit = (int)$limit;
            $offset = (int)$offset;

            $query = "
                SELECT r.*, u.name as reporter_name 
                FROM {$this->table} r 
                JOIN tbl_users u ON r.user_id = u.id 
                $whereStr
                ORDER BY r.created_at DESC
                LIMIT $limit OFFSET $offset
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Failed to get paginated reports");
        }
    }

    public function getTotalReports($status = 'all', $search = '')
    {
        try {
            $params = [];
            $whereClause = [];

            if ($status !== 'all') {
                $whereClause[] = "r.status = ?";
                $params[] = $status;
            }

            if (!empty($search)) {
                $whereClause[] = "(u.name LIKE ? OR r.location LIKE ? OR r.issue_type LIKE ? OR r.description LIKE ?)";
                $searchTerm = "%$search%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            $whereStr = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";

            $query = "
                SELECT COUNT(*) as total
                FROM {$this->table} r 
                JOIN tbl_users u ON r.user_id = u.id 
                $whereStr
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$result['total'];
        } catch (PDOException $e) {
            throw new Exception("Failed to get total reports");
        }
    }

    public function getPaginatedReportsByUserId($user_id, $status = 'all', $search = '', $limit = 10, $offset = 0)
    {
        try {
            $params = [];
            $whereClause = [];

            if ($status !== 'all') {
                $whereClause[] = "r.status = ?";
                $params[] = $status;
            }

            if (!empty($search)) {
                $whereClause[] = "(u.name LIKE ? OR r.location LIKE ? OR r.issue_type LIKE ? OR r.description LIKE ?)";
                $searchTerm = "%$search%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            $whereClause[] = "r.user_id = ?";
            $params[] = $user_id;

            $whereStr = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";

            $limit = (int)$limit;
            $offset = (int)$offset;

            $query = "
                SELECT r.*, u.name as reporter_name 
                FROM {$this->table} r 
                JOIN tbl_users u ON r.user_id = u.id 
                $whereStr
                ORDER BY r.created_at DESC
                LIMIT $limit OFFSET $offset
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Failed to get paginated reports");
        }
    }

    public function getTotalReportsByUserId($user_id, $status = 'all', $search = '')
    {
        try {
            $params = [];
            $whereClause = [];

            if ($status !== 'all') {
                $whereClause[] = "r.status = ?";
                $params[] = $status;
            }

            if (!empty($search)) {
                $whereClause[] = "(u.name LIKE ? OR r.location LIKE ? OR r.issue_type LIKE ? OR r.description LIKE ?)";
                $searchTerm = "%$search%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            $whereClause[] = "r.user_id = ?";
            $params[] = $user_id;

            $whereStr = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";

            $query = "
            SELECT COUNT(*) as total
            FROM {$this->table} r 
            JOIN tbl_users u ON r.user_id = u.id 
            $whereStr
        ";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$result['total'];
        } catch (PDOException $e) {
            throw new Exception("Failed to get total reports");
        }
    }

    public function getReportStatusCount($user_id)
    {
        try {
            $query = "
                SELECT status, COUNT(*) as count
                FROM {$this->table} WHERE user_id = ?
                GROUP BY status
                ORDER BY status
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$user_id]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to get report status count");
        }
    }

    public function getAllReportStatusCount()
    {
        try {
            $query = "
                SELECT status, COUNT(*) as count
                FROM {$this->table}
                GROUP BY status
                ORDER BY status
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to get report status count");
        }
    }
}
