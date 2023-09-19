<?php 



if ($_SERVER['SERVER_NAME'] == 'localhost'){

    define('DBNAME','php');
    define('DBHOST', '127.0.0.1');
    define('DBUSER','php');
    define('DBPASS','php');

    define('ROOT_DIR', 'http://localhost/mvc/public');
}else {
    define('DBNAME','php');
    define('DBHOST', '127.0.0.1');
    define('DBUSER','php');
    define('DBPASS','php');

    define('ROOT_DIR', 'http://'.$_SERVER['SERVER_NAME']);
}

define ('SECRET_KEY','fnkejwfkrfuwehjf');

define ('APP_NAME', 'My Website');
define ('APP_DESC','My Website');


define ('DEBUG', true)

?>