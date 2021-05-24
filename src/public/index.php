<?php
//Front Controller

//common settings
ini_set('display_errors', 1);
error_reporting(E_ALL);


//include files
require_once '../vendor/autoload.php';
require_once '../Components/db_connect.php';
require_once '../Controllers/FrontController.php';
use App\Db\Connection;
use App\Db\Config;

$connection = new Connection(new Config());
$connection->getConnection();

