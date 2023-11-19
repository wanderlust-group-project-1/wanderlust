<?php


session_start();

require '../vendor/autoload.php';
require "../app/core/init.php";

DEBUG  ? ini_set('display_errors',1) :ini_set('display_errors',0);

// die();
$app = new APP;
$app->loadController();


// print_r($_GET);

// print_r($URL);



// loadController();

?>