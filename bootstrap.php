<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require 'vendor/autoload.php';

$app = new \Slim\Slim();
$app->response()->headers->set('Content-Type', 'application/json');

return $app;