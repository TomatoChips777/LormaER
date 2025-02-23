<?php
require_once 'Database.php';
require_once 'Session.php';

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

            // Prepare and execute the insertion query
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, location, issue_type, description, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $location, $issueType, $description, $imagePath]);

            // Fetch the ID of the last inserted report (auto-generated)
            $reportId = $this->db->lastInsertId();

            // Fetch the newly created report data
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
            $stmt->execute([$reportId]);
            $newReport = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return the report data along with the success message
            return [
                'success' => true,
                'message' => 'Report submitted successfully',
                'report' => $newReport // Return the new report's data
            ];
        } catch (PDOException $e) {
            error_log("Report creation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to submit report. Please try again.'
            ];
        }
    }

    // public function createReport($location, $issueType, $description, $imagePath)
    // {
    //     try {

    //         $userId = Session::get('id');

    //         $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, location, issue_type, description, image_path) VALUES (?, ?, ?, ?, ?)");
    //         $stmt->execute([$userId, $location, $issueType, $description, $imagePath]);

    //         return [
    //             'success' => true,
    //             'message' => 'Report submitted successfully'
    //         ];
    //     } catch (PDOException $e) {
    //         error_log("Report creation error: " . $e->getMessage());
    //         return [
    //             'success' => false,
    //             'message' => 'Failed to submit report. Please try again.'
    //         ];
    //     }
    // }

    public function getReportsByUser($userId)
    {
        try {

            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get reports error: " . $e->getMessage());
            return [];
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
            error_log("Get all reports error: " . $e->getMessage());
            return [];
        }
    }
    public function getIssueTypes($filter)
    {
        try {
            $dateCondition = "";

            // Apply date filter based on the selected option
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

            // Fetch data with the selected filter
            $query = "SELECT issue_type, COUNT(*) as count 
                      FROM {$this->table} 
                      WHERE 1 $dateCondition 
                      GROUP BY issue_type 
                      ORDER BY count ASC";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get issue types error: " . $e->getMessage());
            return [];
        }
    }


    public function updateStatus($reportId, $status)
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
            $result = $stmt->execute([$status, $reportId]);

            return [
                'success' => $result,
                'message' => $result ? 'Status updated successfully' : 'Failed to update status'
            ];
        } catch (PDOException $e) {
            error_log("Update status error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update status'
            ];
        }
    }
    public function updateReport($reportId, $location, $issueType, $description, $imagePath, $user_id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET location = ?, issue_type = ?, description = ?, image_path = ? WHERE id = ? and user_id = ?");
            $stmt->execute([$location, $issueType, $description, $imagePath, $reportId, $user_id]);
            return true;
        } catch (PDOException $e) {
            error_log("Update report error: " . $e->getMessage());
            return false;
        }
    }
    public function deleteReport($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id=?");
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error deleting report: " . $e->getMessage());
            return false;
        }
    }
    public function getReportStatusByType($issueType = null)
    {
        try {
            if (!empty($issueType) && $issueType !== 'all') {
                // If filtering by a specific issue type
                $query = "SELECT issue_type, status, COUNT(*) as count 
                      FROM {$this->table} 
                      WHERE issue_type = ? 
                      GROUP BY issue_type, status 
                      ORDER BY issue_type, status";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$issueType]);
            } else {
                // If getting total counts across all issue types
                $query = "SELECT status, COUNT(*) as count 
                      FROM {$this->table} 
                      GROUP BY status 
                      ORDER BY status";
                $stmt = $this->db->prepare($query);
                $stmt->execute();
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get report status by type error: " . $e->getMessage());
            return [];
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

            // Cast limit and offset directly in the query
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

            error_log("Final SQL Query: " . $query);
            error_log("Parameters: " . print_r($params, true));

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Query result count: " . count($result));
            if (count($result) === 0) {
                // Execute a test query to verify data exists
                $testQuery = "SELECT COUNT(*) as total FROM {$this->table} r JOIN tbl_users u ON r.user_id = u.id";
                $testStmt = $this->db->prepare($testQuery);
                $testStmt->execute();
                $testResult = $testStmt->fetch(PDO::FETCH_ASSOC);
                error_log("Test query total records: " . $testResult['total']);

                error_log("No results found. Last SQL error: " . print_r($stmt->errorInfo(), true));
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Get paginated reports error: " . $e->getMessage());
            error_log("SQL State: " . $e->errorInfo[0]);
            error_log("Error Code: " . $e->errorInfo[1]);
            error_log("Error Message: " . $e->errorInfo[2]);
            return [];
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

            // Debug the final query with actual values
            $debugQuery = $query;
            foreach ($params as $param) {
                $debugQuery = preg_replace('/\?/', "'$param'", $debugQuery, 1);
            }
            error_log("Total Count SQL Query: " . $debugQuery);

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Debug output
            error_log("Total count result: " . print_r($result, true));

            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Get total reports error: " . $e->getMessage());
            error_log("SQL State: " . $e->errorInfo[0]);
            error_log("Error Code: " . $e->errorInfo[1]);
            error_log("Error Message: " . $e->errorInfo[2]);
            return 0;
        }
    }


    public function getPaginatedReportsByUserId($user_id, $status = 'all', $search = '', $limit = 10, $offset = 0)
    {
        try {
            $params = [];
            $whereClause = [];

            // Filter by status if provided
            if ($status !== 'all') {
                $whereClause[] = "r.status = ?";
                $params[] = $status;
            }

            // Search filter for reports
            if (!empty($search)) {
                $whereClause[] = "(u.name LIKE ? OR r.location LIKE ? OR r.issue_type LIKE ? OR r.description LIKE ?)";
                $searchTerm = "%$search%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            // Filter by user ID (the user-specific filter)
            $whereClause[] = "r.user_id = ?";
            $params[] = $user_id;

            // Build the WHERE clause
            $whereStr = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";

            // Ensure proper casting of limit and offset
            $limit = (int)$limit;
            $offset = (int)$offset;

            // SQL Query to fetch paginated reports
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
            error_log("Get paginated reports error: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalReportsByUserId($user_id, $status = 'all', $search = '')
    {
        try {
            $params = [];
            $whereClause = [];

            // Filter by status if provided
            if ($status !== 'all') {
                $whereClause[] = "r.status = ?";
                $params[] = $status;
            }

            // Search filter for reports
            if (!empty($search)) {
                $whereClause[] = "(u.name LIKE ? OR r.location LIKE ? OR r.issue_type LIKE ? OR r.description LIKE ?)";
                $searchTerm = "%$search%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            // Filter by user ID
            $whereClause[] = "r.user_id = ?";
            $params[] = $user_id;

            // Build the WHERE clause
            $whereStr = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";

            // SQL query to count the total reports for the user
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
            error_log("Get total reports error: " . $e->getMessage());
            return 0;
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
            error_log("Get report status count error: " . $e->getMessage());
            return [];
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
            error_log("Get report status count error: " . $e->getMessage());
            return [];
        }
    }
}
