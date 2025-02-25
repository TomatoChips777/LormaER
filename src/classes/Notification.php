<?php 
require_once 'Database.php';
require_once 'Session.php';

class Notification
{
    private $database;
    private $db;
    private $tableAdminNotif = 'tbl_admin_notifications';
    private $tableUserNotif = 'tbl_user_notifications';

    public function __construct()
    {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }

    public function createNotification($userId, $reportId, $message)
    {   
        $userRole = Session::get('role');
        try {
            if($userRole == 'admin'){ 
                $query = "INSERT INTO {$this->tableUserNotif} (user_id, report_id, message) VALUES (?, ?, ?)";
                $params = [$userId,$reportId, $message];
            }else{ 
                $query = "INSERT INTO {$this->tableAdminNotif} (user_id, report_id, message) VALUES (?, ?, ?)";
                $params = [$userId,$reportId, $message];
            }

            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($params);
            
            if (!$result) {
                throw new Exception("Failed to insert notification");
            }
            return true;
        } catch (PDOException $e) {
            throw new Exception("Failed to insert notification");
        }
    }

    public function getNotifications()
    {
        $userRole = Session::get('role');
        $userId = Session::get('id');
        try {
            if($userRole == 'admin'){ 
                
                $query = "SELECT n.*, DATE_FORMAT(n.created_at, '%M %d, %Y %h:%i %p') as formatted_date 
                FROM {$this->tableAdminNotif} n WHERE is_read = 0
                ORDER BY n.created_at DESC";
                $params = [];
            }else{ 
                $query = "SELECT n.*, DATE_FORMAT(n.created_at, '%M %d, %Y %h:%i %p') as formatted_date 
                FROM {$this->tableUserNotif} n WHERE is_read = 0 AND user_id = ?
                ORDER BY n.created_at DESC";
                $params = [$userId];
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch notifications");
        }
    }

    public function markAsRead($notificationId)
    {
        $userRole = Session::get('role');
        try {
            if($userRole == 'admin'){
                $query = "UPDATE {$this->tableAdminNotif} SET is_read = 1 WHERE id = ?";
                $params = [$notificationId];
            }else{
                $query = "UPDATE {$this->tableUserNotif} SET is_read = 1 WHERE id = ?";
                $params = [$notificationId];
            }
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Failed to mark notification as read");
        }
    }
}
?>
