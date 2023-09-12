<?php 

if ($_SERVER['SERVER_NAME'] == 'localhost'){

    define('DBNAME','php');
    define('DBHOST', 'localhost');
    define('DBUSER','php');
    define('DBPASS','php');

    define('ROOT_DIR', 'http://localhost/mvc/public');
}else {
    define('ROOT_DIR', 'http://localhost');
}



?>