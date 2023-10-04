<?php


// use Dotenv\Dotenv;
// use Firebase\JWT\JWT;

spl_autoload_register(function (string $classname): void {
    $filename = "../app/models/" . trim(ucfirst($classname), 'Model') . ".php";
    require $filename;
});

require 'functions.php';

require 'config.php';
require 'declarations.php';
require 'Database.php';
require 'Model.php';
require 'Controller.php';
require 'App.php';
require '../app/middlewares/AuthMiddleware.php';
