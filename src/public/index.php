<?php
/**
 * Common settings
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);


/**
 * Include files
 */
require_once '../vendor/autoload.php';
require_once '../Controllers/FrontController.php';

$frontController = new FrontController();
