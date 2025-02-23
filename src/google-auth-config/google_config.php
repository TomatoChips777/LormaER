<?php 
require __DIR__ . '/../../vendor/autoload.php';


$client = new Google\Client;

$client->setClientId('');
$client->setClientSecret("");
$client->setRedirectUri("http://localhost/LormaER/public/authenticate.php");
$client->addScope("email");
$client->addScope("profile");
$client->setPrompt("select_account");
?>

