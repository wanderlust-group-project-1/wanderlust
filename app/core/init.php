<?php


// use Dotenv\Dotenv;
// use Firebase\JWT\JWT;

spl_autoload_register(function (string $classname): void {
    // echo $classname;
    // die();
    // echo trim($classname, "Model");    
    // echo str_replace("Model", "", ucfirst($classname));
    $filename = "../app/models/" . str_replace("Model", "", ucfirst($classname)) . ".php";
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
require '../app/middlewares/UserMiddleware.php';
require '../app/middlewares/AdminMiddleware.php';
require '../app/middlewares/VerifiedMiddleware.php';
require '../app/middlewares/APIMiddleware.php';

require '../app/middlewares/AuthorizationMiddleware.php';

require 'Classes.php';

