<?php
require_once '../../../../config.php';   
require_once CLASSES_PATH . 'Notification.php';

header('Content-Type: application/json');

$notification = new Notification();
$notifications = $notification->getNotifications();

// Debugging: Check if any data is fetched
error_log("Notifications Response: " . print_r($notifications, true));

echo json_encode($notifications);
exit;

?>
