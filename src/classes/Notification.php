<?php 
require_once 'Database.php';
require_once 'Session.php';

class Notification
{
    private $database;
    private $db;
    private $table = 'tbl_notifications';

    public function __construct()
    {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }

    public function createNotification($userId, $reportId, $message)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, report_id, message) VALUES (?, ?, ?)");
            $result = $stmt->execute([$userId,$reportId, $message]);
            
            if (!$result) {
                error_log("Failed to insert notification. Error info: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            return true;
        } catch (PDOException $e) {
            error_log("Notification creation error: " . $e->getMessage());
            return false;
        }
    }

    public function getNotifications()
    {
        try {
            // Get the current user's ID
            // $userId = Session::get('id');
            $limit = 5;
            $stmt = $this->db->prepare("SELECT n.*, DATE_FORMAT(n.created_at, '%M %d, %Y %h:%i %p') as formatted_date 
                                      FROM {$this->table} n WHERE is_read = 0
                                      ORDER BY n.created_at DESC 
                                      LIMIT 5");
            // $stmt = $this->db->prepare("SELECT * FROM {$this->table} ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Notification fetch error: " . $e->getMessage());
            return [];
        }
    }

    public function markAsRead($notificationId)
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET is_read = 1 WHERE id = ?");
            return $stmt->execute([$notificationId]);
        } catch (PDOException $e) {
            error_log("Mark notification as read error: " . $e->getMessage());
            return false;
        }
    }
}
?>
