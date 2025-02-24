<?php
require_once __DIR__ . '/../../../../config.php';
require_once CLASSES_PATH . 'Session.php';
require_once CLASSES_PATH . 'User.php';

// Ensure only admin can access this page
Session::requireAdmin();

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$role = isset($_GET['role']) ? $_GET['role'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$offset = ($page - 1) * $limit;

// Debug request parameters
error_log("Request parameters - Page: $page, Limit: $limit, Offset: $offset, Role: $role, Search: $search");

// Create User instance
$user = new User();

// Get filtered and paginated users
$users = $user->getPaginatedUsers($role, $search, $limit, $offset);
$total = $user->getTotalUsers($role, $search);
$roleCount = $user->getUserRoleCount();


// Debug output
error_log("Users returned: " . print_r($users, true));
error_log("Total users: $total");

$data = [];

if (!empty($users)) {
    foreach ($users as $user) {
        $data[] = [
            'id' => $user['id'],
            'name' => htmlspecialchars($user['name']),
            'email' => htmlspecialchars($user['email']),
            'role' => htmlspecialchars($user['role']),
            'created_at' => date('M d, Y', strtotime($user['created_at'])),
            'report_count' => $user['report_count'],
        ];
    }
}
$roleCountStats = [];
foreach ($roleCount as $counts) {
    $roleCountStats[$counts['role']] = $counts['count'];
}

// Debug final output
error_log("Final JSON data: " . json_encode([
    'data' => $data,
    'total' => $total,
    'pages' => ceil($total / $limit)
]));

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'data' => $data,
    'total' => $total,
    'pages' => ceil($total / $limit),
    'role_counts' => $roleCountStats,
]);
?>
