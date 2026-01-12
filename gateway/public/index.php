<?php
use Slim\Factory\AppFactory;
require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

(require __DIR__ . '/../config/bootstrap.php')($app);
(require __DIR__ . '/../config/routes.php')($app);

$app->run();
