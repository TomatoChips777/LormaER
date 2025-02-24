<?php 
require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . '/src/google-auth-config/google_config.php';
require_once CLASSES_PATH . 'User.php';
require_once CLASSES_PATH . 'Session.php';

if(!isset($_GET['code'])) {
    $url = $client->createAuthUrl();
    header('Location: ' . $url);
    exit();
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);


$client->setAccessToken($token['access_token']);
Session::set('token', $token['access_token']);
$service = new Google\Service\Oauth2($client);
$userInfo = $service->userinfo->get();

$user = new User();
if($user->google_login($userInfo->email, $userInfo->name, $userInfo->picture)) {
    
    Session::start();
    $role = Session::get('role');
    if($role === 'admin') {
        header('Location: users/admin/dashboard.php');
    } elseif($role === 'student') {
        header('Location: users/student/dashboard.php');
    }else{
        $user->logout();
        header('Location: login.php');
    }
    exit();

}else{
    header('Location: login.php');
    exit();
}


?>
