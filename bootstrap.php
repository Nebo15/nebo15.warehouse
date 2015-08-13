<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require 'vendor/autoload.php';

$app = new \Slim\Slim();
$app->response()->headers->set('Content-Type', 'application/json');

$app->container->singleton('modelIps', function() use ($app) {
    return new Models\Ips();
});

$app->container->singleton('logger', function () {
    return new Models\Log(dirname(__FILE__) . '/www/logs/logs.log');
});

return $app;