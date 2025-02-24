<?php 
require __DIR__ . '/../../vendor/autoload.php';


$client = new Google\Client;

$client->setClientId('580568721016-hai6i1uphmpeh6j9mom5if666h9uv42b.apps.googleusercontent.com');
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/LormaER/public/authenticate.php");
$client->addScope("email");
$client->addScope("profile");
$client->setPrompt("select_account");
?>

