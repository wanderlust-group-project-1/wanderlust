<?php


spl_autoload_register(function (string $classname): void {
    $filename = "../app/models/" . trim(ucfirst($classname), 'Model') . ".php";
    require $filename;
});


require 'config.php';
require 'declarations.php';
require 'functions.php';
require 'Database.php';
require 'Model.php';
require 'Controller.php';
require 'App.php';