<?php
/*
1.- config
2.- autoload
3.- bootstrap
4.- request
5.- controller
6.- database
7.- model
8.- view  */
ini_set('display_errors', 1);

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)) . DS);
define('APP_PATH', ROOT . 'application' . DS);

require_once APP_PATH . 'Config.php';
require_once APP_PATH . 'Autoload.php';

try{
    Bootstrap::run(new Request);
}
catch(Exception $e){
    echo $e->getMessage();
}

?>
