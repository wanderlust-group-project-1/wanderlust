<?php

use Dotenv\Dotenv;

function loadEnv()
{
    $dotenvPath = __DIR__ . '/../../.env';


    if (file_exists($dotenvPath)) {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        return $_ENV;
    } else {
        // .env file does not exist, handle the situation or simply return false
        return false;
    }
}


$_ENV = loadEnv();



if ($_ENV) {
    define('DBNAME', $_ENV['DB_NAME']);
    define('DBHOST', $_ENV['DB_HOST']);
    define('DBUSER', $_ENV['DB_USER']);
    define('DBPASS', $_ENV['DB_PASSWORD']);

    // Object storage url
    define('OSURL', $_ENV['OSURL']);

    define('GOOGLE_MAPS_API_KEY', $_ENV['GOOGLE_MAPS_API_KEY']);

    define('ROOT_DIR', 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']);
} else if ($_SERVER['SERVER_NAME'] == 'localhost' && $_SERVER['SERVER_PORT'] == 80) {

    define('DBNAME', 'php');
    define('DBHOST', '127.0.0.1');
    define('DBUSER', 'php');
    define('DBPASS', 'php');

    define('ROOT_DIR', 'http://localhost/wanderlust/public');
    define('OSURL', 'http://localhost/wanderlust/public/uploads/');

} else {
    define('DBNAME', 'php');
    define('DBHOST', '127.0.0.1');
    define('DBUSER', 'php');
    define('DBPASS', 'php');

    define('ROOT_DIR', 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']);
}

define('SECRET_KEY', 'fnkejwfkrfuwehjf');

define('APP_NAME', 'My Website');
define('APP_DESC', 'My Website');


define('DEBUG', true);
