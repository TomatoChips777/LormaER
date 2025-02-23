<?php
require_once __DIR__ . '/../config.php';
require_once CLASSES_PATH . 'User.php';
require_once CLASSES_PATH . 'Session.php';

$user = new User();
$user->logout();

header('Location: index.php');
exit();
?>
