<?php 
require_once __DIR__ . '/../../../../config.php';
require_once CLASSES_PATH . 'Session.php';
require_once CLASSES_PATH . 'User.php';


// Ensure only admin can access this page
Session::requireAdmin();

// Get POST data
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$role = isset($_POST['role']) ? $_POST['role'] : '';

// Validate input
if (!$user_id || !in_array($role, ['admin', 'student', 'other'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Update report status
$user = new User();
$result = $user->updateUserRole($user_id, $role);
// Return JSON response
header('Content-Type: application/json');
echo json_encode($result);
?>

