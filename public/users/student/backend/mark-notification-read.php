<?php
require_once __DIR__ . '/../../../../config.php';
require_once CLASSES_PATH . 'Session.php';
require_once CLASSES_PATH . 'Notification.php';

// Ensure user is logged in
Session::start();
if (!Session::isLoggedIn()) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notificationId = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0;
    
    if ($notificationId) {
        $notification = new Notification();
        $result = $notification->markAsRead($notificationId);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    exit;
}
