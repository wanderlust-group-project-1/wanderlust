<?php


session_start();

require '../vendor/autoload.php';
require "../app/core/init.php";

DEBUG  ? ini_set('display_errors',1) :ini_set('display_errors',0);
// ini_set('memory_limit', '-1');

// die();
$app = new APP;
$app->loadController();

// // $_SESSION['url']  = "ABC";
// echo $_SESSION['url'];

// print_r($_GET);

// print_r($URL);



// loadController();

?>