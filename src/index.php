<?php

use Slim\Factory\AppFactory;

require_once __DIR__.'/vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__.'/config')->load();

$app = AppFactory::create();

$app->get('/', 'geolite2php\Controller\GeoIP:getRemote');
$app->get('/remote', 'geolite2php\Controller\GeoIP:getRemote');
$app->get('/local', 'geolite2php\Controller\GeoIP:getLocal');

$app->run();
